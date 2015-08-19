#Silverstripe Vend API integration
This is basically a wrapper for the [VendAPI package](https://github.com/brucealdridge/VendAPI) by @brucealdridge for Silverstripe.

The main difference is implementation for OAuth2 with token management.
##Requires
- Silverstripe 3.1
- The [vendapi/vendapi](https://github.com/brucealdridge/VendAPI) package
 
##Install

using Composer: `composer require heyday/silverstripe-vend:dev/master`

##Setup

you need to sign up for a Vend developer account: [https://developers.vendhq.com/](https://developers.vendhq.com/)

When you have done so, set your details in a yml config file under `VendAPI`.

eg:
 
`mysite/_config/vend.yml` :

```
################
###VEND SETUP###
################

VendAPI:
  clientID: g6ohGHJgJHKtuyfUTYfjVjhGhfTUYfTU
  clientSecret: iyGFktyFKUYlguKHkjHUHUiGHKuHGKj
  redirectURI: admin/vend/authorise

```

After a `dev/build` and a `flush` you should have a new Menu called `Vend Admin` where the next steps of the setup are done.

##Implementation

To use you just need to inject the `Heyday\Vend\Connection` where you want to use it and follow the docs of the [VendAPI package](https://github.com/brucealdridge/VendAPI) for the other methods.


eg:

###injection config

```
Injector:

  Page_Controller:
    properties:
      VendConnection: %$Heyday\Vend\Connection
      
```
###implementation in Page_Controller

```
    protected $vendConnection;

    public function setVendConnection($vendConnection)
    {
        $this->vendConnection = $vendConnection;
    }

    public function VendTest()
    {
        $products = $this->vendConnection->getProducts();
    }
```        
  