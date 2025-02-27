<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "";
    $phone = isset($_POST["phone"]) ? htmlspecialchars($_POST["phone"]) : "";
    $date = isset($_POST["date"]) ? htmlspecialchars($_POST["date"]) : "";
    $details = isset($_POST["details"]) ? htmlspecialchars($_POST["details"]) : "";
    
    // reCAPTCHA 検証
    $recaptcha_secret = "your-secret-key";
    $recaptcha_response = $_POST["g-recaptcha-response"];
    $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
    
    $recaptcha_data = [
        "secret" => 6LfIe-QqAAAAAAgNA_oMCAvSZI6_cVNa3sgZC38m,
        "response" => $recaptcha_response
    ];
    
    $options = [
        "http" => [
            "header" => "Content-Type: application/x-www-form-urlencoded\r\n",
            "method" => "POST",
            "content" => http_build_query($recaptcha_data)
        ]
    ];
    
    $context = stream_context_create(["http" => $options]);
    $verify = file_get_contents($recaptcha_url, false, $context);
    $captcha_success = json_decode($verify);
    
    if (!$captcha_success->success) {
        die("reCAPTCHA verification failed. Please try again.");
    }
    
    // メール送信設定
    $to = "mizukoujiomakase1132@gmail.com"; // ここに受信するメールアドレスを設定
    $from = "thsm.shuto0418@gmail.com"; // 送信元メールアドレスを設定
    $subject = !empty($date) ? "予約フォームからの新規予約" : "見積もりフォームからの問い合わせ";
    
    $message = "
        名前: $name\n
    ". (!empty($email) ? "メール: $email\n" : "") .
       (!empty($phone) ? "電話番号: $phone\n" : "") .
       (!empty($date) ? "希望日時: $date\n" : "") .
       (!empty($details) ? "詳細: $details\n" : "");
    
    $headers = "From: $from\r\n" .
               "Reply-To: $email\r\n" .
               "Content-Type: text/plain; charset=UTF-8\r\n";
    
    if (mail($to, $subject, $message, $headers)) {
        echo "送信成功！お問い合わせありがとうございます。";
    } else {
        echo "送信に失敗しました。後ほどお試しください。";
    }
} else {
    echo "不正なリクエストです。";
}
?>
