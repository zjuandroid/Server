<?php

namespace QL\Ext\Lib;

/**
 *
 * @desc  HTTP ������, ֧�� CURL �� Socket, Ĭ��ʹ�� CURL , ���ֶ�ָ��
 *              useCurl ���� curl ��չû�а�װʱ, ��ʹ�� Socket
 *              Ŀǰ֧�� get �� post ��������ʽ
 *
 * @example
 *
1. ���� get ����:

$http = new Http();         // ʵ��������
$result =  $http->get('http://weibo.com/at/comment');

2. ���� post ����:

$http = new Http();         // ʵ��������
$result = $http->post('http://someurl.com/post-new-article', array('title'=>$title, 'body'=>$body) );

3. ģ���¼ ( post �� get ͬʱʹ��, ���� cookie �洢״̬ ) :

$http = new Http();         // ʵ��������
$http->setCookiepath(substr(md5($username), 0, 10));        // ���� cookie, ����Ƕ���û�����Ļ�
// �ύ post ����
$loginData = $http->post('http://login.sina.com.cn/sso/login.php?client=ssologin.js(v1.3.19)', array('username'=>$username, 'loginPass'=>$password) );
$result =  $http->get('http://weibo.com/at/comment');

4. ���� initialize �������ö�� config ��Ϣ

$httpConfig['method']     = 'GET';
$httpConfig['target']     = 'http://www.somedomain.com/index.html';
$httpConfig['referrer']   = 'http://www.somedomain.com';
$httpConfig['user_agent'] = 'My Crawler';
$httpConfig['timeout']    = '30';
$httpConfig['params']     = array('var1' => 'testvalue', 'var2' => 'somevalue');

$http = new Http();
$http->initialize($httpConfig);

$result = $http->result;

5. ���ӵ�����:

$http = new Http();
$http->useCurl(false);      // ��ʹ�� curl
$http->setMethod('POST');       // ʹ�� POST method

// ���� POST ����
$http->addParam('user_name' , 'yourusername');
$http->addParam('password'  , 'yourpassword');

// Referrer
$http->setReferrer('https://yourproject.projectpath.com/login');

// ��ʼִ������
$http->execute('https://yourproject.projectpath.com/login/authenticate');
$result = $http->getResult();

6. ��ȡ������ basic auth ������

$http = new Http();

// Set HTTP basic authentication realms
$http->setAuth('yourusername', 'yourpassword');

// ��ȡĳ����������Ӧ�õ� feed
$http->get('http://www.someblog.com/protected/feed.xml');

$result = $http->result;

 *
 * @from http://www.phpfour.com/lib/http
 * @since Version 0.1
 * @original author      Md Emran Hasan <phpfour@gmail.com>
 * @modify by       Charlie Jade
 */

class Http
{
    /**  Ŀ������ @var string */
    var $target;

    /**  Ŀ�� URL �� host @var string */
    var $host;

    /**  ����Ŀ��Ķ˿� @var integer */
    var $port;

    /** ����Ŀ��� path @var string */
    var $path;

    /** ����Ŀ��� schema  @var string */
    var $schema;

    /** ����� method (GET ���� POST)  @var string */
    var $method;

    /** ���������  @var array */
    var $params;

    /**  ����ʱ��� cookie ����  @var array */
    var $cookies;

    /**  ���󷵻ص� cookie ���� @var array  */
    var $_cookies;

    /** ����ʱʱ��, Ĭ���� 25 @var integer */
    var $timeout;

    /** �Ƿ�ʹ�� cURL , Ĭ��Ϊ TRUE @var boolean */
    var $useCurl;

    /**   referrer ��Ϣ @var string */
    var $referrer;

    /** ����ͻ��� User agent  @var string */
    var $userAgent;

    /**  Contains the cookie path (to be used with cURL) @var string */
    var $cookiePath;

    /**  �Ƿ�ʹ�� Cookie @var boolean  */
    var $useCookie;

    /** �Ƿ�Ϊ��һ�����󱣴� Cookie @var boolean */
    var $saveCookie;

    /** HTTP Basic Auth �û��� (for authentication) @var string */
    var $username;

    /** HTTP Basic Auth ���� (for authentication) @var string */
    var $password;

    /** ����Ľ���� @var string */
    var $result;

    /** ���һ������� headers ��Ϣ  @var array */
    var $headers;

    /** Contains the last call's http status code @var string  */
    var $status;

    /** �Ƿ���� http redirect ��ת  @var boolean */
    var $redirect;

    /** ��� http redirect ������ @var integer */
    var $maxRedirect;

    /** ��ǰ�����ж��ٸ� URL  @var integer */
    var $curRedirect;

    /** ������� @var string */
    var $error;

    /** Store the next token  @var string */
    var $nextToken;

    /** �Ƿ�洢 bug ��Ϣ @var boolean  */
    var $debug;

    /** Stores the debug messages  @var array @todo will keep debug messages */
    var $debugMsg;

    /**  Constructor for initializing the class with default values. @return void   */
    public function __construct()
    {
        // �ȳ�ʼ��
        $this->clear();
    }

    /**
     * ��ʼ��������Ϣ
     * Initialize preferences
     *
     * This function will take an associative array of config values and
     * will initialize the class variables using them.
     *
     * Example use:
     *
     * <pre>
     * $httpConfig['method']     = 'GET';
     * $httpConfig['target']     = 'http://www.somedomain.com/index.html';
     * $httpConfig['referrer']   = 'http://www.somedomain.com';
     * $httpConfig['user_agent'] = 'My Crawler';
     * $httpConfig['timeout']    = '30';
     * $httpConfig['params']     = array('var1' => 'testvalue', 'var2' => 'somevalue');
     *
     * $http = new Http();
     * $http->initialize($httpConfig);
     * </pre>
     *
     * @param array Config values as associative array
     * @return void
     */
    public function initialize($config = array())
    {
        $this->clear();
        foreach ($config as $key => $val)
        {
            if (isset($this->$key))
            {
                $method = 'set' . ucfirst(str_replace('_', '', $key));

                if (method_exists($this, $method))
                {
                    $this->$method($val);
                }
                else
                {
                    $this->$key = $val;
                }
            }
        }
    }

    /**
     * ��ʼ������
     *
     * Clears all the properties of the class and sets the object to
     * the beginning state. Very handy if you are doing subsequent calls
     * with different data.
     *
     * @return void
     */
    public function clear()
    {
        // Set the request defaults
        $this->host         = '';
        $this->port         = 0;
        $this->path         = '';
        $this->target       = '';
        $this->method       = 'GET';
        $this->schema       = 'http';
        $this->params       = array();
        $this->headers      = array();
        $this->cookies      = array();
        $this->_cookies     = array();

        // Set the config details
        $this->debug        = FALSE;
        $this->error        = '';
        $this->status       = 0;
        $this->timeout      = '25';
        $this->useCurl      = TRUE;
        $this->referrer     = '';
        $this->username     = '';
        $this->password     = '';
        $this->redirect     = TRUE;

        // Set the cookie and agent defaults
        $this->nextToken    = '';
        $this->useCookie    = FALSE;
        $this->saveCookie   = FALSE;
        $this->maxRedirect  = 3;
        $this->cookiePath   = 'cookie.txt';
        $this->userAgent    = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.63 Safari/535.7';
    }

    /** ����Ŀ�� @return void */
    public function setTarget($url)
    {
        $this->target = $url;
    }

    /** ���� http ���󷽷�  @param string HTTP method to use (GET or POST)  @return void */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /** ���� referrer URL @param string URL of referrer page @return void */
    public function setReferrer($referrer)
    {
        $this->referrer = $referrer;
    }

    /**  ���� User agent  @param string Full user agent string @return void */
    public function setUseragent($agent)
    {
        $this->userAgent = $agent;
    }

    /** �������� timeout  @param integer Timeout delay in seconds @return void */
    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;
    }

    /** ����  cookie path (ֻ֧��cURL ) @param string File location of cookiejar @return void */
    public function setCookiepath($path)
    {

        $this->cookiePath = $path;
        $this->useCookie(TRUE);
        $this->saveCookie(TRUE);
    }

    /** ����������� parameters @param array GET or POST ���������� @return void  */
    public function setParams($dataArray)
    {
        $this->params = array_merge($this->params, $dataArray);
    }

    /** ���� basic http auth ����֤ @param string �û���  @param string ����  @return void */
    public function setAuth($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /** ���������ת�� @param integer Maximum number of redirects @return void */
    public function setMaxredirect($value)
    {
        $this->maxRedirect = $value;
    }

    /** ��Ӷ�һ���µ��������� @param string Name of the parameter @param string Value of the paramete  @return void  */
    public function addParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /** ��� cookie ��������  @param string Name of cookie  @param string Value of cookie */
    public function addCookie($name, $value)
    {
        $this->cookies[$name] = $value;
    }

    /** �Ƿ�ʹ�� curl, Ĭ�� true, false Ϊʹ�� socket  */
    public function useCurl($value = TRUE)
    {
        if (is_bool($value))
        {
            $this->useCurl = $value;
        }
    }

    /** �Ƿ�ʹ�� cookie , Ĭ��Ϊ false  @param boolean Whether to use cookies or not  @return void  */
    public function useCookie($value = FALSE)
    {
        $this->useCookie = $value;
    }

    /** �Ƿ�ʹ�� cookie , �Թ���һ������ʹ�� @param boolean Whether to save persistent cookies or not @return void  */
    public function saveCookie($value = FALSE)
    {
        $this->saveCookie = $value;
    }

    /** �Ƿ���� 302 ��ת @param boolean Whether to follow HTTP redirects or not  */
    public function followRedirects($value = TRUE)
    {
        $this->redirect = $value;
    }

    /** ��ȡ�����  @return string output of execution */
    public function getResult()
    {
        return $this->result;
    }

    /** ��ȡ���һ�����ص� headers ���� */
    public function getHeaders()
    {
        return $this->headers;
    }

    /** ��ȡ�����״̬��  */
    public function getStatus()
    {
        return $this->status;
    }

    /** ��ȡ������д���   */
    public function getError()
    {
        return $this->error;
    }

    /** ִ��һ�� http get ����  */
    public function get($url, $data=array()){
        return $this->execute($url, '', 'GET', $data);
    }

    /** ִ��һ�� http post ����  */
    public function post($url, $data=array()){
        return $this->execute($url, '', 'POST', $data);
    }

    /**
     * ʹ�õ�ǰ������, ����һ�� HTTP ����
     *
     * @param string URL of the target page (optional)
     * @param string URL of the referrer page (optional)
     * @param string ���󷽷� (GET or POST) (optional)
     * @param array ��������, key �� value ��Ӧ������ (optional)
     * @return string ����Ľ����
     */
    public function execute($target = '', $referrer = '', $method = '', $data = array())
    {
        // Populate the properties
        $this->target = ($target) ? $target : $this->target;
        $this->method = ($method) ? $method : $this->method;
        $this->referrer = ($referrer) ? $referrer : $this->referrer;

        // Add the new params
        if (is_array($data) && count($data) > 0)
        {
            $this->params = array_merge($this->params, $data);
        }

        // Process data, if presented
        if(is_array($this->params) && count($this->params) > 0)
        {
            // Get a blank slate
            $tempString = array();

            // Convert data array into a query string (ie animal=dog&sport=baseball)
            foreach ($this->params as $key => $value)
            {
                if(strlen(trim($value))>0)
                {
                    $tempString[] = $key . "=" . urlencode($value);
                }
            }

            $queryString = join('&', $tempString);
        }

        // ��� cURL û�а�װ��ʹ�� fscokopen ִ������
        $this->useCurl = $this->useCurl && in_array('curl', get_loaded_extensions());

        // GET method configuration
        if($this->method == 'GET')
        {
            if(isset($queryString))
            {
                $this->target = $this->target . "?" . $queryString;
            }
        }

        // Parse target URL
        $urlParsed = parse_url($this->target);

        // Handle SSL connection request
        if ($urlParsed['scheme'] == 'https')
        {
            $this->host = 'ssl://' . $urlParsed['host'];
            $this->port = ($this->port != 0) ? $this->port : 443;
        }
        else
        {
            $this->host = $urlParsed['host'];
            $this->port = ($this->port != 0) ? $this->port : 80;
        }

        // Finalize the target path
        $this->path   = (isset($urlParsed['path']) ? $urlParsed['path'] : '/') . (isset($urlParsed['query']) ? '?' . $urlParsed['query'] : '');
        $this->schema = $urlParsed['scheme'];

        // Pass the requred cookies
        $this->_passCookies();

        // Process cookies, if requested
        if(is_array($this->cookies) && count($this->cookies) > 0)
        {
            // Get a blank slate
            $tempString   = array();

            // Convert cookiesa array into a query string (ie animal=dog&sport=baseball)
            foreach ($this->cookies as $key => $value)
            {
                if(strlen(trim($value)) > 0)
                {
                    $tempString[] = $key . "=" . urlencode($value);
                }
            }

            $cookieString = join('&', $tempString);
        }

        // Do we need to use cURL
        if ($this->useCurl)
        {
            // Initialize PHP cURL handle
            $ch = curl_init();

            // GET method configuration
            if($this->method == 'GET')
            {
                curl_setopt ($ch, CURLOPT_HTTPGET, TRUE);
                curl_setopt ($ch, CURLOPT_POST, FALSE);
            }
            // POST method configuration
            else
            {
                if(isset($queryString))
                {
                    curl_setopt ($ch, CURLOPT_POSTFIELDS, $queryString);
                }

                curl_setopt ($ch, CURLOPT_POST, TRUE);
                curl_setopt ($ch, CURLOPT_HTTPGET, FALSE);
            }

            // Basic Authentication configuration
            if ($this->username && $this->password)
            {
                curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
            }
            //TODO
            curl_setopt($ch, CURLOPT_USERPWD, '13186978264' . ':' . 'wc63650312');
            dump($this);

            // Custom cookie configuration
            if($this->useCookie && isset($cookieString))
            {
                curl_setopt ($ch, CURLOPT_COOKIE, $cookieString);
            }

            curl_setopt($ch, CURLOPT_HEADER,         array('Accept-Language: zh-cn','Connection: Keep-Alive','Cache-Control: no-cache'));
            curl_setopt($ch, CURLOPT_NOBODY,         FALSE);                // Return body

            curl_setopt($ch, CURLOPT_COOKIEJAR,      $this->cookiePath);    // cookie �ļ�
            curl_setopt($ch, CURLOPT_COOKIEFILE,      $this->cookiePath);    // cookie �ļ�

            curl_setopt($ch, CURLOPT_TIMEOUT,        $this->timeout);       // Timeout
            curl_setopt($ch, CURLOPT_USERAGENT,      $this->userAgent);     // Webbot name
            curl_setopt($ch, CURLOPT_URL,            $this->target);        // Target site
            curl_setopt($ch, CURLOPT_REFERER,        $this->referrer);      // Referer value

            curl_setopt($ch, CURLOPT_VERBOSE,        FALSE);                // Minimize logs
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);                // No certificate
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->redirect);      // Follow redirects
            curl_setopt($ch, CURLOPT_MAXREDIRS,      $this->maxRedirect);   // Limit redirections to four
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);                 // �Ƿ��� string ��ʽ����

            // Get the target contents
            $content = curl_exec($ch);

            // Get the request info
            $curl_info = curl_getinfo($ch);
            $header_size = $curl_info["header_size"];

            // ��ֵ�����
            $this->result = substr($content, $header_size);

            $reader = explode("\r\n\r\n", trim(substr($content, 0, $header_size)));
            $this->status = $curl_info['http_code'];

            // Parse the headers
            $this->_parseHeaders( explode("\r\n\r\n", trim(substr($content, 0, $header_size))) );

            // Store the error (is any)
            $this->_setError(curl_error($ch));

            // Close PHP cURL handle
            curl_close($ch);
        }
        else
        {
            // Get a file pointer
            $filePointer = fsockopen($this->host, $this->port, $errorNumber, $errorString, $this->timeout);

            // We have an error if pointer is not there
            if (!$filePointer)
            {
                $this->_setError('Failed opening http socket connection: ' . $errorString . ' (' . $errorNumber . ')');
                return FALSE;
            }

            // Set http headers with host, user-agent and content type
            $requestHeader  = $this->method . " " . $this->path . "  HTTP/1.1\r\n";
            $requestHeader .= "Host: " . $urlParsed['host'] . "\r\n";
            $requestHeader .= "User-Agent: " . $this->userAgent . "\r\n";
            $requestHeader .= "Content-Type: application/x-www-form-urlencoded\r\n";

            // Specify the custom cookies
            if ($this->useCookie && $cookieString != '')
            {
                $requestHeader.= "Cookie: " . $cookieString . "\r\n";
            }

            // POST method configuration
            if ($this->method == "POST")
            {
                $requestHeader.= "Content-Length: " . strlen($queryString) . "\r\n";
            }

            // Specify the referrer
            if ($this->referrer != '')
            {
                $requestHeader.= "Referer: " . $this->referrer . "\r\n";
            }

            // Specify http authentication (basic)
            if ($this->username && $this->password)
            {
                $requestHeader.= "Authorization: Basic " . base64_encode($this->username . ':' . $this->password) . "\r\n";
            }

            $requestHeader.= "Connection: close\r\n\r\n";

            // POST method configuration
            if ($this->method == "POST")
            {
                $requestHeader .= $queryString;
            }

            // We're ready to launch
            fwrite($filePointer, $requestHeader);

            // Clean the slate
            $responseHeader = '';
            $responseContent = '';

            // 3...2...1...Launch !
            do
            {
                $responseHeader .= fread($filePointer, 1);
            }
            while (!preg_match('/\\r\\n\\r\\n$/', $responseHeader));

            // Parse the headers
            $this->_parseHeaders($responseHeader);

            // Do we have a 301/302 redirect ?
            if (($this->status == '301' || $this->status == '302') && $this->redirect == TRUE)
            {
                if ($this->curRedirect < $this->maxRedirect)
                {
                    // Let's find out the new redirect URL
                    $newUrlParsed = parse_url($this->headers['location']);

                    if ($newUrlParsed['host'])
                    {
                        $newTarget = $this->headers['location'];
                    }
                    else
                    {
                        $newTarget = $this->schema . '://' . $this->host . '/' . $this->headers['location'];
                    }

                    // Reset some of the properties
                    $this->port   = 0;
                    $this->status = 0;
                    $this->params = array();
                    $this->method = 'GET';
                    $this->referrer = $this->target;

                    // Increase the redirect counter
                    $this->curRedirect++;

                    // Let's go, go, go !
                    $this->result = $this->execute($newTarget);
                }
                else
                {
                    $this->_setError('Too many redirects.');
                    return FALSE;
                }
            }
            else
            {
                // Nope...so lets get the rest of the contents (non-chunked)
                if ($this->headers['transfer-encoding'] != 'chunked')
                {
                    while (!feof($filePointer))
                    {
                        $responseContent .= fgets($filePointer, 128);
                    }
                }
                else
                {
                    // Get the contents (chunked)
                    while ($chunkLength = hexdec(fgets($filePointer)))
                    {
                        $responseContentChunk = '';
                        $readLength = 0;

                        while ($readLength < $chunkLength)
                        {
                            $responseContentChunk .= fread($filePointer, $chunkLength - $readLength);
                            $readLength = strlen($responseContentChunk);
                        }

                        $responseContent .= $responseContentChunk;
                        fgets($filePointer);
                    }
                }

                // Store the target contents
                $this->result = chop($responseContent);
            }
        }

        // There it is! We have it!! Return to base !!!
        return $this->result;
    }

    /** ���� header ��Ϣ*/
    private function _parseHeaders($responseHeader)
    {
        // Break up the headers
        $headers = $responseHeader;

        // Clear the header array
        $this->_clearHeaders();

        // Get resposne status
        if($this->status == 0)
        {
            // Oooops !
            if(!eregi($match = "^http/[0-9]+\\.[0-9]+[ \t]+([0-9]+)[ \t]*(.*)\$", $headers[0], $matches))
            {
                $this->_setError('Unexpected HTTP response status');
                return FALSE;
            }

            // Gotcha!
            $this->status = $matches[1];
            array_shift($headers);
        }

        // Prepare all the other headers
        foreach ($headers as $header)
        {
            // Get name and value
            $headerName  = strtolower($this->_tokenize($header, ':'));
            $headerValue = trim(chop($this->_tokenize("\r\n")));

            // If its already there, then add as an array. Otherwise, just keep there
            if(isset($this->headers[$headerName]))
            {
                if(gettype($this->headers[$headerName]) == "string")
                {
                    $this->headers[$headerName] = array($this->headers[$headerName]);
                }

                $this->headers[$headerName][] = $headerValue;
            }
            else
            {
                $this->headers[$headerName] = $headerValue;
            }
        }

        // Save cookies if asked
        if ($this->saveCookie && isset($this->headers['set-cookie']))
        {
            $this->_parseCookie();
        }
    }

    /** ȥ������ header ��Ϣ */
    private function _clearHeaders()
    {
        $this->headers = array();
    }

    /** ���� COOKIE */
    private function _parseCookie()
    {
        // Get the cookie header as array
        if(gettype($this->headers['set-cookie']) == "array")
        {
            $cookieHeaders = $this->headers['set-cookie'];
        }
        else
        {
            $cookieHeaders = array($this->headers['set-cookie']);
        }

        // Loop through the cookies
        for ($cookie = 0; $cookie < count($cookieHeaders); $cookie++)
        {
            $cookieName  = trim($this->_tokenize($cookieHeaders[$cookie], "="));
            $cookieValue = $this->_tokenize(";");

            $urlParsed   = parse_url($this->target);

            $domain      = $urlParsed['host'];
            $secure      = '0';

            $path        = "/";
            $expires     = "";

            while(($name = trim(urldecode($this->_tokenize("=")))) != "")
            {
                $value = urldecode($this->_tokenize(";"));

                switch($name)
                {
                    case "path"     : $path     = $value; break;
                    case "domain"   : $domain   = $value; break;
                    case "secure"   : $secure   = ($value != '') ? '1' : '0'; break;
                }
            }

            $this->_setCookie($cookieName, $cookieValue, $expires, $path , $domain, $secure);
        }
    }

    /** ���� cookie , Ϊ��һ��������׼�� */
    private function _setCookie($name, $value, $expires = "" , $path = "/" , $domain = "" , $secure = 0)
    {
        if(strlen($name) == 0)
        {
            return($this->_setError("No valid cookie name was specified."));
        }

        if(strlen($path) == 0 || strcmp($path[0], "/"))
        {
            return($this->_setError("$path is not a valid path for setting cookie $name."));
        }

        if($domain == "" || !strpos($domain, ".", $domain[0] == "." ? 1 : 0))
        {
            return($this->_setError("$domain is not a valid domain for setting cookie $name."));
        }

        $domain = strtolower($domain);

        if(!strcmp($domain[0], "."))
        {
            $domain = substr($domain, 1);
        }

        $name  = $this->_encodeCookie($name, true);
        $value = $this->_encodeCookie($value, false);

        $secure = intval($secure);

        $this->_cookies[] = array( "name"      =>  $name,
            "value"     =>  $value,
            "domain"    =>  $domain,
            "path"      =>  $path,
            "expires"   =>  $expires,
            "secure"    =>  $secure
        );
    }

    /** cookie  ���ݼ�����  */
    private function _encodeCookie($value, $name)
    {
        return($name ? str_replace("=", "%25", $value) : str_replace(";", "%3B", $value));
    }

    /** ����ȷ�� cookie �������ǰ���� */
    private function _passCookies()
    {
        if (is_array($this->_cookies) && count($this->_cookies) > 0)
        {
            $urlParsed = parse_url($this->target);
            $tempCookies = array();

            foreach($this->_cookies as $cookie)
            {
                if ($this->_domainMatch($urlParsed['host'], $cookie['domain']) && (0 === strpos($urlParsed['path'], $cookie['path']))
                    && (empty($cookie['secure']) || $urlParsed['protocol'] == 'https'))
                {
                    $tempCookies[$cookie['name']][strlen($cookie['path'])] = $cookie['value'];
                }
            }

            // cookies with longer paths go first
            foreach ($tempCookies as $name => $values)
            {
                krsort($values);
                foreach ($values as $value)
                {
                    $this->addCookie($name, $value);
                }
            }
        }
    }

    /** ƥ������ */
    private function _domainMatch($requestHost, $cookieDomain)
    {
        if ('.' != $cookieDomain{0})
        {
            return $requestHost == $cookieDomain;
        }
        elseif (substr_count($cookieDomain, '.') < 2)
        {
            return false;
        }
        else
        {
            return substr('.'. $requestHost, - strlen($cookieDomain)) == $cookieDomain;
        }
    }

    /** ����ǰ�������Ǻ��õ� */
    private function _tokenize($string, $separator = '')
    {
        if(!strcmp($separator, ''))
        {
            $separator = $string;
            $string = $this->nextToken;
        }

        for($character = 0; $character < strlen($separator); $character++)
        {
            if(gettype($position = strpos($string, $separator[$character])) == "integer")
            {
                $found = (isset($found) ? min($found, $position) : $position);
            }
        }

        if(isset($found))
        {
            $this->nextToken = substr($string, $found + 1);
            return(substr($string, 0, $found));
        }
        else
        {
            $this->nextToken = '';
            return($string);
        }
    }

    /** ���ô�����Ϣ */
    private function _setError($error)
    {
        if ($error != '')
        {
            $this->error = $error;
            return $error;
        }
    }
}

?>