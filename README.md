# Introduction
This is a Guzzle plugin to support Bearer Authentication as specified in RFC 
6750.

# Example Use

    <?php
    require_once 'vendor/autoload.php';

    use fkooman\Guzzle\Plugin\BearerAuth\BearerAuth;
    use Guzzle\Http\Client;
    use Guzzle\Http\Exception\ClientErrorResponseException;

    try {
        $client = new Client();

        $bearerAuth = new BearerAuth("12345", function($msg) {
            echo $msg . PHP_EOL;
            // delete the used token or mark it invalid before
            // trying again
        });
        $client->addSubscriber($bearerAuth);
        $client->get('http://example.org/api')->send();
    } catch (ClientErrorResponseException $e) {
        // if it was a 401 we handled it in the callback
        if(401 !== $e->getResponse()->getStatusCode()) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
