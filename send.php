<?php
use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$login = getenv('LOGIN');
$password = getenv('PASSWORD');

// Файлы phpmailer
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

// Переменные пользователя
$name = $_POST['name'];
$email = $_POST['email'];
$text = $_POST['text'];
$file = $_FILES['myfile'];

// Формирование письма

$title = "Заголовок";
$body = "
<h2>Новое письмо</h2>
<b>Имя:</b> $name<br>
<b>Почта:</b> $email<br><br>
<b>Сообщения:</b><br>$text
";


// Настройки phpmailer

$mail = new PHPMailer\PHPMailer\PHPMailer();

try {
    $mail->isSMTP();
    $mail->CharSet = "UTF-8";
    $mail->SMTPAuth = true;
    $mail->Debugoutput = function ($str, $level) {
        $GLOBALS['status'][] = $str; };
    
    //Настройка почты

    $mail->Host = 'smtp.yandex.ru';// SMTP сервера почты
    $mail->Username = $login;// Логин на почте
    $mail->Password = $password;// Пароль на почте
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->setFrom('kirill.ochkurov1@yandex.ru', 'kirill');// Адрес почты и имя отправителя

    // Получатель письма

    $mail->addAddress('k1rilo-1991@yandex.ru');
    $mail->addAddress('kirya.ochkurov1991@mail.ru');

    // Прикрепление файлов к письму

    if (!empty($file['name'][0])) {
        for ($ct = 0; $ct < count($file['tmp_name']); $ct++) {
            $uploadfile = tempnam(sys_get_temp_dir(), sha1($file['name'][$ct]));
            $filename = $file['naem'][$ct];
            if (move_uploaded_file($file['tmp_name'][$ct], $uploadfile)) {
                $mail->addAttachment($uploadfile, $filename);
                $rfile[] = "Файл $filename прикреплен";
            } else {
                $rfile[] = "Не удалось прикрепить файл $filename";
            }
        }
    }

    // Отправка сообщения

    $mail->isHTML(true);
    $mail->Subject = $title;
    $mail->Body = $body;

    // Проверяем отправку сообщения

    if ($mail->send()) {
        $result = "success";} else {
        $result = "error";}

} catch (Exception $e) {
    $result = "error";
    $status = "Сообщение не было отправлено. Причина ошибки: {$mail->ErrorInfo}";

}

// Результаты

echo json_encode(["result" => $result, "resultfile" => $rfile, "status" => $status]);