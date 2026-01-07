<?php
// Koneksi ke MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_powerone";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// === CURL Login Request ===
$curl = curl_init();

$postBody = json_encode([
    "email" => "finance@embajeans.com",
    "password" => "3Mb4123!"
]);

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://mcp.matahari.co.id:81/Auth/Login',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $postBody,
  CURLOPT_HTTPHEADER => array(
    'Accept: application/json',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
$curl_errno = curl_errno($curl);
$curl_error = curl_error($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// Tampilkan info dasar CURL/HTTP (berguna saat debugging)
echo "<h3>Debug curl / http info</h3>";
echo "<b>HTTP status:</b> " . htmlspecialchars($http_code) . "<br>";
if ($curl_errno) {
    echo "<b>cURL error (no):</b> $curl_errno<br>";
    echo "<b>cURL error:</b> " . htmlspecialchars($curl_error) . "<br>";
}
echo "<hr>";

// Tampilkan response mentah selalu — agar bisa dilihat "di sini juga"
echo "<h3>Raw response (mentah)</h3>";
echo "<pre style='white-space:pre-wrap;word-wrap:break-word;'>" . htmlspecialchars($response) . "</pre>";
echo "<hr>";

// === Decode JSON response ===
$data = json_decode($response, true);
$json_error = json_last_error();
$json_error_msg = json_last_error_msg();

if ($json_error !== JSON_ERROR_NONE) {
    echo "<h3>JSON decode error</h3>";
    echo "<b>json_last_error:</b> $json_error<br>";
    echo "<b>json_last_error_msg:</b> " . htmlspecialchars($json_error_msg) . "<br>";
    // Kalau perlu, hentikan di sini atau lanjutkan sesuai kebutuhan
}

// Tampilkan struktur array hasil decode (jika berhasil decode)
if (is_array($data)) {
    echo "<h3>Decoded JSON (array)</h3>";
    echo "<pre style='white-space:pre-wrap;word-wrap:break-word;'>" . htmlspecialchars(print_r($data, true)) . "</pre>";
} else {
    echo "<h3>Decoded JSON tidak dalam bentuk array (mungkin null)</h3>";
}

// Cek apakah struktur sesuai
if (isset($data["result"]["userInfo"])) {
    $userInfo = $data["result"]["userInfo"];

    // Ambil field dengan safety checks
    $userId = $conn->real_escape_string($userInfo["userId"] ?? '');
    $firstName = $conn->real_escape_string($userInfo["firstName"] ?? '');
    $lastName = $conn->real_escape_string($userInfo["lastName"] ?? '');
    $username = $conn->real_escape_string($userInfo["username"] ?? '');
    $email = $conn->real_escape_string($userInfo["email"] ?? '');
    $vendorId = isset($userInfo["vendorId"]) ? intval($userInfo["vendorId"]) : "NULL";
    $requirePasswordChange = !empty($userInfo["requirePasswordChange"]) ? 1 : 0;
    $purchaseType = $conn->real_escape_string($userInfo["purchaseType"] ?? '');
    $role = $conn->real_escape_string($userInfo["role"] ?? '');
    $token = $conn->real_escape_string($data["result"]["token"] ?? '');
    $expiration = $conn->real_escape_string($data["result"]["expiration"] ?? null);
    $keycloakToken = $conn->real_escape_string($data["result"]["keycloakToken"] ?? '');
    $success = !empty($data["success"]) ? 1 : 0;
    $message = $conn->real_escape_string($data["message"] ?? '');

    // Simpan ke database
    $sql = "INSERT INTO mcp_token (
                userId, firstName, lastName, username, email, vendorId,
                requirePasswordChange, purchaseType, role, token,
                expiration, keycloakToken, success, message
            ) VALUES (
                '$userId', '$firstName', '$lastName', '$username', '$email', $vendorId,
                $requirePasswordChange, '$purchaseType', '$role', '$token',
                " . ($expiration ? "'$expiration'" : "NULL") . ", '$keycloakToken', $success, '$message'
            )";

    if ($conn->query($sql) === TRUE) {
        echo "<h3>✅ Data login berhasil disimpan ke database.</h3>";
        // Tampilkan semua token yang ada di table mcp_token (echo semua)
        $res = $conn->query("SELECT id, userId, vendorId, token, keycloakToken, expiration, created_at FROM mcp_token ORDER BY id DESC");
        if ($res && $res->num_rows > 0) {
            echo "<h3>All tokens in DB (latest first)</h3>";
            echo "<table border='1' cellpadding='6' cellspacing='0'>";
            echo "<tr><th>id</th><th>userId</th><th>vendorId</th><th>expiration</th><th>created_at</th><th>token (truncated)</th><th>keycloakToken (truncated)</th></tr>";
            while ($row = $res->fetch_assoc()) {
                // truncate token for safety display (but you can remove substr if you want full)
                $t = htmlspecialchars(substr($row['token'], 0, 200));
                $k = htmlspecialchars(substr($row['keycloakToken'], 0, 200));
                echo "<tr>
                        <td>".$row['id']."</td>
                        <td>".htmlspecialchars($row['userId'])."</td>
                        <td>".htmlspecialchars($row['vendorId'])."</td>
                        <td>".htmlspecialchars($row['expiration'])."</td>
                        <td>".htmlspecialchars($row['created_at'])."</td>
                        <td><textarea style='width:400px;height:80px;'>$t</textarea></td>
                        <td><textarea style='width:400px;height:120px;'>$k</textarea></td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<b>Info:</b> Tidak ada record token di tabel mcp_token (atau query gagal).<br>";
        }
    } else {
        echo "<h3>❌ Error saat menyimpan ke DB:</h3>";
        echo htmlspecialchars($conn->error);
    }
} else {
    // Jika struktur tidak sesuai, tampilkan pesan dan response (sudah ditampilkan di atas),
    // plus sarankan apa yang perlu dicek.
    echo "<h3>⚠️ Response tidak sesuai format yang diharapkan.</h3>";
    echo "<p>Pastikan API mengembalikan JSON dengan struktur <code>{ result: { userInfo: {...}, token: '...', ... }, success: true }</code></p>";
    echo "<p>Debug tambahan:</p>";
    echo "<ul>";
    echo "<li>HTTP status: " . htmlspecialchars($http_code) . "</li>";
    echo "<li>JSON error: " . htmlspecialchars($json_error_msg) . "</li>";
    echo "</ul>";
    // (Raw response sudah ditampilkan di bagian atas)
}

$conn->close();
?>
