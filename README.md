#Silverstripe Vend API integration
This is basically a wrapper for the [VendAPI package](https://github.com/brucealdridge/VendAPI) by @brucealdridge for Silverstripe.

The main difference is implementation for OAuth2 with token management.
##Requires
- Silverstripe 3.1
- The [vendapi/vendapi](https://github.com/brucealdridge/VendAPI) package
 
##Install

using Composer: `composer require heyday/silverstripe-vend:dev/master`


##Setup

First you need to sign up for a Vend developer account and create an app: [https://developers.vendhq.com/](https://developers.vendhq.com/)

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

Also map the route you have setup as a redirectURI in your Vend app to the `Heyday\Vend\SilverStripe\Authorise_Controller`.
This controller handles the data returned by the Vend API when you first authorise the app to connect to your store.

eg if I have setup `http://mysite.com/admin/vend/authorise` as my RedirectURI on my Vend app:

`mysite/_config/routing.yml` :

```
---
Name: vendroutes
After: 'framework/routes#coreroutes'
---
Director:
  rules:
    'admin/vend/authorise': 'Heyday\Vend\SilverStripe\Authorise_Controller'
---

```


After a `dev/build` and a `flush` you should have a new Menu called `Vend Admin` where the next steps of the setup are done.

##Implementation

To use you just need to inject the `Heyday\Vend\Connection` where you want to use it and follow the docs of the [VendAPI package](https://github.com/brucealdridge/VendAPI) for the other methods.


eg:

###injection config

```
Injector:

  SomePage_Controller:
    properties:
      VendConnection: %$Heyday\Vend\Connection
      
```
###implementation in SomePage_Controller

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
  