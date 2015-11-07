        // First, include Requests
        include('../library/Requests.php');
        // Next, make sure Requests can load internal classes
        Requests::register_autoloader();
        
        //set form data for authentication
        $data = array('login_email' => "login", 'login_password' => "password");
        
        //create a cookie jar to use for our requests
        $c = new Requests_Cookie_Jar([]);
        
        //create a session object for requests. You could set your cookie jar in constructor, but I find it cleaner setting it right after
        //session object basically remember all the headers/options/data for reuse between requests, and merge those with the ones from individual requests params
        $session = new Requests_Session('https://www.your-site.com/');
        
        //set headers
        $session->headers['Accept'] = 'text/html';
        $session->useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0';
        $session->options = ['cookies' => $c];//set cookie jar
        
        //post form authentication, headers/options already set for all requests that use this session
        $response = $session->post('/fr/Login/Authenticate', [], $data );

        //we're now authenticated, now we can retrieve info for the logged in user from another page

        //set form data to retrieve target stats
        $data = array(  'Period-datestart' => $dateD,
                        'Period-dateend' => $dateF);
        $response = $session->request('/statistics/index', [], $data );//default request type is get
        
        //display response
        var_dump($response);
