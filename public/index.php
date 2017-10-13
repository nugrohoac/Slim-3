<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;

require '../vendor/autoload.php';


$app = new \Slim\App;
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});


// require once here
require_once('../app/api/books.php');
require_once('../app/api/config.php');
require_once('user.php');
require_once('operasipasar.php');

//include '../app/api/books.php';

$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});

$app->post('/decode',function($request, $response){
    $nama = $request->getParsedBody('nama');
    echo $nama;
});

//displau all data
$app->get('/get/book', function($request, $response){
    $time = time(); //time now
    $key = secretKey();
    $token = array(
        "iss" => "yippytech.com",
        "iat" => $time,
        "exp" => $time + (3600 * 50),
        "data" => [
            "nama" => "Nugroho",
            "dept" => "Ilkom"
        ]
    );
    $jwt = JWT::encode($token, $key, "HS256");

    $con = connect();
    $query = "SELECT * FROM books";
    $result = $con->query($query);
    while($row = $result->fetch_assoc()){
        $data['data'][] = $row;
    }
    $data['token'] = $jwt;
    if(isset($data)){
        header('Content-Type: application/json');
        return json_encode($data);
    }
});

$app->post('/validate', function($request, $response){
    $auth = $request->getHeaderLine("Authorization");
    $token;
    $key = secretKey();
    if (preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
        $token = $matches[1];
        $jwt = JWT::decode($token, $key, array("HS256"));
        //print_r($jwt);
        $unencodedData = (array) $jwt;
        $unencodedData = (array)$unencodedData['data'];
        echo $unencodedData = $unencodedData['nama'];
    }
    
    
});

//display single data
$app->get('/single/data/{book_id}',function($request, $response){
    $book_id = $request->getAttribute('book_id');
    $con = connect();
    $query = "SELECT * FROM books WHERE book_id = $book_id";
    $result = $con->query($query);
    //checl data base on book_id
    $data['data'][] = $result->fetch_assoc();
    header('Content-Type: application/json');
    return json_encode($data);
});

//get data
$app->post('/masuk', function($request, $response){
    $con = connect();
    $query = "INSERT INTO books (book_title,author,amazon_url) VALUES (?,?,?)";
    $stmt = $con->prepare($query);
    $judul = $request->getParsedBody()['judul'];
    $author = $request->getParsedBody()['author'];
    $amazon_url = $request->getParsedBody()['url'];
    $stmt->bind_param("sss",$judul,$author,$amazon_url);
    $stmt->execute();
    $data = ambil();
    return json_encode($data);
});

$app->post('/update/{book_id}', function($request, $response){
    $con = connect(); 
    $book_id = $request->getAttribute('book_id');
    $query = "UPDATE books SET amazon_url = ? WHERE book_id = $book_id";
    $stmt = $con->prepare($query);
    $amazon_url = $request->getParsedBody()['ubah'];
    //echo $book_id,$amazon_url;
    $stmt->bind_param("s",$amazon_url);
    $stmt->execute();
    $data = ambil();
    return json_encode($data);
});

$app->get('/delete/{book_id}', function($request, $response){
    $con = connect(); 
    $book_id = $request->getAttribute('book_id');
    $query = "DELETE FROM books WHERE book_id = $book_id";
    $stmt = $con->query($query);
    $data = ambil();
    return json_encode($data);
});



function ambil(){
    $con = connect();
    $query = "SELECT * FROM books";
    $result = $con->query($query);
    while($row = $result->fetch_assoc()){
        $data['data'][] = $row;
    }
    if(isset($data)){
        header('Content-Type: application/json');
        return $data;
    }
}

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

$app->get('/email', function($request, $response){
       $url = 'www.google.com';
       $mail = new PHPMailer();
       $mail->CharSet =  "utf-8";
       $mail->IsSMTP();
       $mail->SMTPAuth = true;
       $mail->Username = "portalharga.ipb@gmail.com";
       $mail->Password = "portalharga1234";
       $mail->SMTPSecure = "ssl";  
       $mail->Host = "smtp.gmail.com";
       $mail->Port = "465";
    
       $mail->setFrom('portalharga.ipb@gmail.com', 'portal harga');
       $mail->AddAddress('nugrohoac96@gmail.com', 'Nugroho');
    
       $mail->Subject  =  'using PHPMailer';
       $mail->IsHTML(true);
       $mail->Body    = "The new password is {$url}.
            <br>
            <br>
            ini baris ke tiga
            <br>
            <br>
            ini baris lanjut";
     
        if($mail->Send())
        {
           return "Message was Successfully Send :)";
        }
        else
        {
           return "Mail Error - >".$mail->ErrorInfo;
        }
});

$app->post('/user/add',function($request, $response){
    $username = $request->getParsedBody()['username'];
    $name = $request->getParsedBody()['name'];
    $password = $request->getParsedBody()['password'];
    $email = $request->getParsedBody()['email'];
    $role = $request->getParsedBody()['role'];
    $address = $request->getParsedBody()['address'];
    $no_telepon = $request->getParsedBody()['no_telepon'];
    //call function add user in user.php
    addUser($username, $name, $password, $email, $role, $address, $no_telepon);

});

//login
$app->post('/user/login',function($request, $response){
    //get data from input
    $username = $request->getParsedBody()['username'];
    $password = $request->getParsedBody()['password'];
    //login
    $time = time(); //time now
    $key = secretKey();
    $token = array(
        "iss" => "yippytech.com",
        "iat" => $time,
        "exp" => $time + (3600 * 50),
        "data" => [
            "username" => $username,
            "name" => $name
        ]
    );
    $jwt = JWT::encode($token, $key, "HS256");
    $result['data'] = login($username, $password);
    $result['token'] = $jwt;
    if(isset($result)){
        header('Content-Type: application/json');
        return json_encode($result);
    }
});

//get all operasi pasar
$app->get('/operasiPasar/get',function($request, $response){
    $auth = $request->getHeaderLine("Authorization");
    if (preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
        $connect = connect();
        $key = secretKey();
        $token = $matches[1];
        $jwt = JWT::decode($token, $key, array("HS256"));
        //print_r($jwt);
        allOperasiPasar();
    } else {
        return "Need token";
    }
});

$app->get('/time',function($request, $response){
    $data = array(
        "time1" => time(),
        "tiime2" => time() + 3500
    );
    print_r($data);
});


$app->run();