<?php 
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'cehlab');
define('DB_USER', 'root');
define('DB_KEY', '');
define('DB_CHARSET', 'UTF8MB4');

class connect {

	private $host;
	private $dbname;
	private $user;
	private $pass;
	private $charset;
	private $dsn;
	private $options;
	
	function con() {
		//Variables
		$this->host = DB_HOST;
		$this->dbname = DB_NAME;
		$this->user = DB_USER;
		$this->pass = DB_KEY;
		$this->charset = DB_CHARSET;
		$this->dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
		$this->options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // highly recommended
		PDO::ATTR_EMULATE_PREPARES => false, // ALWAYS! ALWAYS! ALWAYS!
		PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
		];
		$db = new PDO($this->dsn, $this->user, $this->pass, $this->options);
		try {
			$db;
			return $db; 
		} catch (PDOException $error){
			return "Connection failed!: " . $error->getMessage();
			die();
		}
	}
}

class plug extends connect {

	private $login;
	private $session;
	public $status;
	public $loggedIn;

	function __construct() {
		$this->login = "SELECT _password FROM `accounts` WHERE `_email`=:_email";
		$this->session = "SELECT _id FROM `accounts` WHERE `_email`=:_email";
		$this->status = null;
	}

	function login() {
		if(isset($_POST['submit']) && isset($_POST['_email']) && isset($_POST['_password'])){
			$connect = new connect;
			$email = filter_var($_POST['_email'], FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL);
			$password = filter_var($_POST['_password']);
			if($connect) {
				$query = $this->login;
				$sql = $connect->con()->prepare($query);
				$sql->execute([
					'_email' => $email
				]);
				$result = $sql->fetchColumn();
				// Not using hashes here...
				if($password === $result){ 
					$fetch = $this->session;
					$set = $connect->con()->prepare($fetch);
					$set->execute([
						'_email'=> $email
					]);
					$result = $set->fetchColumn();
					$_SESSION['user_session'] = $result;
					$this->loggedIn = $_SESSION['user_session'];
					$this->status = 'Succesful';
				} else {
					$this->status = 'Eh, Heeh, Matatizo your trying to login eeh..';
				}	
			}
		}
	}

	function logout() {
		if($this->loggedIn && isset($_POST['back'])) {
			$this->loggedIn = false;
			unset($_SESSION['user_session']);
		}
	}
}

$ini = new plug;
$ini->login();
$ini->logout();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CEH LAB</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <section
        class="bg-gradient-to-b from-slate-800 to-slate-900 w-screen h-screen flex flex-col justify-center items-center space-y-6">
        <?php if(!$ini->loggedIn) { ?>
        <form action="" method='POST' enctype='multipart/form-data' class="w-full max-w-sm flex flex-col space-y-8">
            <span class="w-full flex flex-col items-center justify-center">
                <h1 class="text-4xl text-white font-bold capitalize">session <span
                        class="text-green-500">hijacking</span> lab
                </h1>
            </span>
            <span class="h-[1px] bg-white/[.2] w-full flex"></span>
            <span class="w-full h-full flex flex-col space-y-2">
                <label for="email" class="text-white ">Email</label>
                <input id="email" name="_email" class="p-2 rounded-md focus:outline-none duration-300 easeInOut"
                    type="email" placeholder="email" />
            </span>
            <span class="w-full h-full flex flex-col space-y-2">
                <label for="password" class="text-white">Password</label>
                <input id="password" name="_password" class="p-2 rounded-md focus:outline-none duration-300 easeInOut"
                    type="password" placeholder="password" />
            </span>
            <button class="bg-green-500 rounded-md p-2" type="submit" name="submit">
                Login
            </button>
            <?php if($ini->status) { ?>
            <span class="w-full max-w-[330px] py-2 px-4 bg-yellow-500 rounded-md">
                <p class="font-bold">Notification</p>
                <p class="font-light text-sm"><?php echo $ini->status; ?></p>
            </span>
            <?php } ?>
        </form>

        <?php } else { ?>
        <form method="POST" action="" class="w-full max-w-sm flex flex-col items-center justify-center space-y-7">
            <span class="w-full text-center flex flex-col space-y-2 items-center">
                <h4 class="text-white text-5xl whitespace-nowrap">Karibu <span class="text-green-500">Mr,
                        Matatizo</span></h4>
                <p class="text-white text-xl font-light">You have succesfully logged in</p>
            </span>
            <span class="h-[1px] bg-white/[.2] w-full flex"></span>
            <button class="w-full bg-green-500 rounded-md p-2" type="submit" name="back">
                Aya, Go Home Now it's getting late
            </button>
        </form>
        <?php } ?>
    </section>
</body>

</html>