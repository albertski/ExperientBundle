<?php

namespace ExperientBundle\Services;

/**
 * Handles Experient SOAP API calls.
 */
class Experient
{

  /**
   * @var string
   *   Experient username.
   */
  private $username;

  /**
   * @var string
   *   Experient password.
   */
  private $password;

  /**
   * @var string
   *   Experient showcode.
   */
  private $showcode;

  /**
   * @var string
   *   Experient SQL Environment. Allowed values are: PROD, QA, DEV.
   */
  private $headerSQLEnvironment = 'PROD';

  /**
   * @var integer
   *   Page size (limit is 500).
   */
  private $pagedResultsPageSize = 50;

  /**
   * @var string
   *   Export time. Allowed values are CSV and None.
   */
  private $fTPFileExportType = 'None';

  /**
   * @var string
   *   use UTC Format;
   */
  private $useUTCFormat = '';

  /**
   * @var string
   *   Experient account domain.
   */
  private $userAccountDomain;

  /**
   * @var string
   *  Soap wsdl url.
   */
  private $wsdl;

  /**
   * $var string
   *   Soap namspace url.
   */
  private $namespace;

  /**
   * @var object
   *   Soap client object.
   */
  private $client;

  /**
   * Default constructor.
   *
   * @param $userName
   * @param $password
   * @param $showcode
   */
  public function __construct($userName, $password, $showcode, $userAccountDomain, $wsdl, $namespace)
  {
    $this->username = $userName;
    $this->password = $password;
    $this->showcode = $showcode;
    $this->userAccountDomain = $userAccountDomain;
    $this->wsdl = $wsdl;
    $this->namespace = $namespace;

    $this->setClient();
    $this->setHeader();
  }

  /**
   * Set SOAP client.
   *
   * @param integer $trace
   * @param integer $exceptions
   */
  public function setClient($trace = 1, $exceptions = 1)
  {
    $this->client = new \SoapClient($this->wsdl, array(
      "trace" => $trace,
      "exceptions" => $exceptions,
    ));
  }

  /**
   * Set Header SQL Environment.
   *
   * @param string $sqlEnvironment
   */
  public function setHeaderSQLEnvironment($sqlEnvironment)
  {
    $this->headerSQLEnvironment = $sqlEnvironment;
  }

  /**
   * Set page size.
   *
   * @param integer integer $size
   */
  public function setPagedResultsPageSize($size)
  {
    $this->pagedResultsPageSize = $size;
  }

  /**
   * Set export type.
   *
   * @param string $exportType
   */
  public function setFTPFileExportType($exportType)
  {
    $this->fTPFileExportType = $exportType;
  }

  /**
   * Set use UTC format.
   *
   * @param string $format
   */
  public function setUseUTCFormat($format)
  {
    $this->useUTCFormat = $format;
  }

  /**
   * Set Header.
   *
   * @param string $method
   *   The header method.
   */
  public function setHeader($method = 'DataExportHeader')
  {
    // Body of the Soap Header.
    $headerBody = (object) array(
      'HeaderShowCode' => $this->showcode,
      'HeaderUsername' => $this->username,
      'HeaderPassword' => $this->password,
      'HeaderSQLEnvironment' => $this->headerSQLEnvironment,
      'PagedResultsPageSize' => $this->pagedResultsPageSize,
      'FTPFileExportType' => $this->fTPFileExportType,
      'FTPUsername' => $this->fTPFileExportType,
      'UserAccountDomain' => $this->userAccountDomain,
      'UseUTCFormat' => $this->useUTCFormat,
    );

    // Create Soap Header.
    $header = new \SOAPHeader($this->namespace, $method, $headerBody);

    // Set the Headers of Soap Client.
    $this->client->__setSoapHeaders($header);
  }

  /**
   * Called to start a Paged DataExport.
   *
   * @param datetime $beginDate
   *   Begin date in the following format: 2015-07-30T22:23:17+02:00.
   * @param datetime $endDate
   *   End date in the following format: 2015-07-30T22:23:17+02:00.
   * @param string $dataType
   *   The ExportDataType.
   * @param string $orderByClause
   *   Sort clause.
   *
   * @return string $result
   *   XML Response.
   */
  public function initializePagedPull($beginDate, $endDate, $dataType = 'Registrant', $orderByClause = '')
  {
    $requestParameters = (object) array(
      'dataType' => $dataType,
      'BeginDate' => $beginDate,
      'EndDate' => $endDate,
      'orderByClause' => $orderByClause,
    );
    $response = $this->client->InitializePagedPull($requestParameters);

    return $this->getResult($response, 'InitializePagedPullResult');
  }

  /**
   * Return a string of XML-formatted RegistrantList elements depicting all or a
   * single page of registrants whose UpdateDate is between the Begin Date and
   * End Date range provided.
   *
   * @param datetime $beginDate
   *   Begin date in the following format: 2015-07-30T22:23:17+02:00.
   * @param datetime $endDate
   *   End date in the following format: 2015-07-30T22:23:17+02:00.
   * @param string $pageToken
   *   The page token returned from a call like initializePagedPull().
   * @param integer $currentPage
   *   The current page.
   *
   * @return string $result
   *   XML Response.
   */
  public function pullRegistrantList($beginDate, $endDate, $pageToken, $currentPage)
  {
    $requestParameters = (object) array(
      'BeginDate' => $beginDate,
      'EndDate' => $endDate,
      'pageToken' => $pageToken,
      'currentPage' => $currentPage,
    );
    $response = $this->client->PullRegistrantList($requestParameters);

    return $this->getResult($response, 'PullRegistrantListResult');
  }

  /**
   * Return a string of XML-formatted DemographicsLookupList elements depicting
   * the Demographic Codes for a Show.
   *
   * @param string $pageToken
   *   The page token returned from a call like initializePagedPull().
   * @param integer $currentPage
   *   The current page.
   *
   * @return string response
   *   XML Response.
   */
  public function pullDemographicsLookupList($pageToken, $currentPage)
  {
    $requestParameters = (object) array(
      'pageToken' => $pageToken,
      'currentPage' => $currentPage,
    );
    $response = $this->client->PullDemographicsLookupList($requestParameters);

    return $this->getResult($response, 'PullDemographicsLookupListResult');
  }

  /**
   * Return a string of XML-formatted DemographicsLookupList elements depicting
   * the Demographic Codes and all Freeform demographics for a show.
   *
   * @return string response
   *   XML Response.
   */
  public function PullDemographicsLookupListWithFreeform()
  {
    $response = $this->client->PullDemographicsLookupListWithFreeform();

    return $this->getResult($response, 'PullDemographicsLookupListWithFreeformResult');
  }

  /**
   * Called to end a Paged DataExport. Removes references to the Token for the
   * search.
   *
   * @param string $pageToken
   *
   * @return string response
   *   XML Response.
   */
  public function finalizePagedPull($pageToken)
  {
    $requestParameters = (object) array(
      'pageToken' => $pageToken,
    );
    $response = $this->client->FinalizePagedPull($requestParameters);

    return $this->getResult($response, 'FinalizePagedPullResult');
  }

  /**
   * Get result from soap api response.
   *
   * @param object $response
   *   The soap response.
   * @param $property
   *   The property name.
   *
   * @return mixed
   */
  private function getResult($response, $property)
  {
    $result = FALSE;

    if (isset($response->{$property})) {
      $result = $response->{$property};
    }

    return $result;
  }

}
