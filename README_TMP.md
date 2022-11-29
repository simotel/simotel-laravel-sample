# Simotel Laravel Connect Sample Application

This is a laravel sample application to demonstrate how you can connect to simotel in Laravel.  
We use **[simotel-laravel-connect](https://github.com/nasimtelecom/simotel-laravel-connect)** package to connect simotel and laravel together.

## Preparing laravel and install simotel-laravel-connect package
#### Step 1: install laravel with compser
First of all you must prepare a laravel application, if you haven't install it yet, install laravel with composer:
```
composer create-project --prefer-dist laravel/laravel simotel-connect
```
#### Step 2: install simotel-laravel-connect
Install simotel-laravel-connect package with composer:
``` 
composer require nasimtelecom/simotel-laravel-connect
```

#### Step 3: publish config
Use artisan command to publish simotel connect config file in laravel config folder:
```.
php artisan vendor:publish --provider="NasimTelecom\Simotel\Laravel\SimotelLaravelServiceProvider"
```

```php
// config/laravel-simotel.php

[
    'smartApi' => [
        'apps' => [
            '*' => "\YourApp\SmartApiAppClass",
        ],
    ],
    'ivrApi' => [
        'apps' => [
            '*' => "\YourApp\IvrApiAppClass",
        ],
    ],
    'trunkApi' => [
        'apps' => [
            '*' => "\YourApp\TrunkApiApp",
        ],
    ],
    'extensionApi' => [
        'apps' => [
            '*' => "\YourApp\ExtensionApiAppClass",
        ],
    ],
    'simotelApi' => [
        'server_address' => 'http://yourSimotelServer/api/v4',
        'api_auth' => 'basic',  // simotel api authentication: basic,token,both
        'api_user' => 'apiUser',
        'api_pass' => 'apiPass',
        'api_key' => 'apiToken',
    ],
];
```
## Simote API (SA) 
SA is a set of APIs that start by sending a request from the web service side to Simotel, this service is created in the RestAPI standard format.

#### Step 1: create api acount in simotel web intreface

<img src="https://github.com/nasimtelecom/laravel-connect-sample/blob/main/public/images/simotel.png?raw=true" width="400">

Visit [Simotel Docs](https://doc.mysup.ir/docs/simotel/callcenter-docs/maintenance/api_accounts) for more details.

#### Step 2: edit simotel config file 
```php
// config/laravel-simotel.php


'simotelApi' => [
        'server_address' => 'http://yourSimotelServer/api/v4',
        'api_auth' => 'basic',  // basic,token,both (simotel api authentication)
        'api_user' => 'apiUser',
        'api_pass' => 'apiPass',
        'api_key' => 'apiToken',
    ],
```
`api_auth`: Acording to [Simotel Docs](https://doc.mysup.ir/docs/api/v4/callcenter_api/SimoTelAPI/settings) Simotel athenticate and authorize you in 3 ways:
* `basic` Basic Authentication
* `token` Api Key (Token)
* `both` Both Basic Authentication and Api Key (Token)

#### Step 3: connect to simotel
In your controller use `Simotel` facade to connect to simotel:
```php
// app\Http\Controller\SimotelConnectController.php

public function searchUsers(){

    // The data will be sent to Simotel server as a request body
    $data = [
        "alike"=>false,
        "conditions"=>["name"=>"200"],
    ];

    try{
         // Sending request to simotel server
        $res = Simotel::connect("pbx/users/search",$data);
    }
    catch(\Exception $e){
        die($e->getMessage());
    }
   

    // Determines whether the transaction was successful or not
    // In other words, if the response status code is 
    // between 200~299 then isOk() will return true 
    if(!$res->isOk())
        die("There is a problem");

    // Or you can get response status code
    $statusCode = $res->getStatusCode();

    // Simotel will return a json response,
    // to cast it to array use toArray() method
    // it will be an array like this:
    // [
    //      "success" => true/false, 
    //      "message" => "Simotel Error Message"
    //      "data"    =>  [response data array]    
    // ]
    // success: determine wether transaction by simotel is ok or not
    // message: this is simotel response message
    // that tell us why transactoion did not completed
    $res->toArray();

    // Simotel Success is true or false
    if(!$res->isSuccess())
        // Get Simotel message if isSuccess()==false
        die($res->getMessage());

    // Get Simotel response data array
    $users = $res->getData();

    
}

```
## Simotel Event Api (SEA)
Consider that you want to listen ti CdrEvent from simotel and store cdr data in database.
#### Step 1: Make and register listener(s)

Make a listener with artisan command:
```
php artisan make:listener StoreSimotelCdrInDatabase
```
Register listener in `EventServiceProvider` and connect it to `Nasim\Simotel\Laravel\Events\SimotelEventCdr`
```php
 protected $listen = [
        Nasim\Simotel\Laravel\Events\SimotelEventCdr::class => [
            StoreSimotelCdrInDatabase::class,
        ],
    ];
```

there is the list of Simotel Event classes that you can use:

| Event Name    | Simotel Event Class                                           |
|     ---       |        ---                                                    |
| Cdr           | Nasim\Simotel\Laravel\Events\SimotelEventCdr::class           |
| CdrQueue      | Nasim\Simotel\Laravel\Events\SimotelEventCdrQueue::class      |
| ExtenAdded    | Nasim\Simotel\Laravel\Events\SimotelEventExtenAdded::class    |
| ExtenRemoved  | Nasim\Simotel\Laravel\Events\SimotelEventExtenRemoved::class  |
| IncomingCall  | Nasim\Simotel\Laravel\Events\SimotelEventIncomingCall::class  |
| IncomingFax   | Nasim\Simotel\Laravel\Events\SimotelEventIncomingFax::class   |
| NewState      | Nasim\Simotel\Laravel\Events\SimotelEventNewState::class      |
| OutgoingCall  | Nasim\Simotel\Laravel\Events\SimotelEventOutgoingCall::class  |
| Survey        | Nasim\Simotel\Laravel\Events\SimotelEventSurvey::class        |
| Transfer      | Nasim\Simotel\Laravel\Events\SimotelEventTransfer::class      |
| VoiceMail     | Nasim\Simotel\Laravel\Events\SimotelEventVoiceMail::class     |
| VoiceMailEmail| Nasim\Simotel\Laravel\Events\SimotelEventVoiceMailEmail::class|

you can collect SimotelEvent data (like cdr data) in listener:
```php
// app/Listeners/StoreSimotelCdrInDatabase.php

    public function handle($event)
    {
        $cdrData = $event->apiData();
    }
```
### Step 2: dispatch Simotel Event
Now define route in api.php and dispatch event by this commands:
```php
// routes/api.php

Route::get("simotel/events",function(Request $request, $event){

        try {
           Simotel::eventApi()->dispatch($event,$request->all());
        } catch (\Exception $exception) {
            die("error: " . $exception->getMessage());
        }

});
```
After defining this route, the path `http://yourAppUrl/api/simotel/events` get the `SimotelEventApi` request and dispatch coresponded event automaticaly.


#### Step 3: Simotel Event Api Setting
Now you can put your application api url in Simotel config. for more information look at [Simotel Document](https://doc.mysup.ir/docs/simotel/callcenter-docs/maintenance/settings/api_settings).
## Simotel SmartApi

```php
// routes/api.php

Route::get("simotel/smartApi",function(Request $request){

    try {
        $respond = Simotel::smartApi($request->all())->toArray();
        return response()->json($respond);
    } catch (\Exception $exception) {
        die("error: " . $exception->getMessage());
    }

});
```
