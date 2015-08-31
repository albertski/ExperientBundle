ExperientBundle
===============

Used for Symfony2 project to use the Experient SOAP API.

# Installation

Get the bundle with composer by running this command at the root of your symfony project.

    composer require albertski/experient-bundle


# Enable the bundle

To start using the bundle, register the bundle in your application's kernel class:

    // app/AppKernel.php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new ExperientBundle\ExperientBundle(),
            // ...
        );
    }

# Configuration

Edit `app/config/config.yml`.

    experient:
      username: %experient.username%
      password: %experient.password%
      showcode: %experient.showcode%
      accountDomain: %experient.accountDomain%
      wsdl: %experient.wsdl%
      namespace: %experient.namespace%

Edit parameters.yml. Add paramaters and add the parameter values.

    parameters:
        .....
        .....
        experient.username:
        experient.password:
        experient.showcode:
        experient.accountDomain:
        experient.wsdl:
        experient.namespace:

# Usage

You now have access to the experient service.

    // In a controller
    $experient = $this->get('experient');
