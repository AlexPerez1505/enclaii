<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$nombre = trim($data['nombre'] ?? '');
$institucion = trim($data['institucion'] ?? '');
$email = trim($data['email'] ?? '');
$telefono = trim($data['telefono'] ?? '');
$tipo = trim($data['tipo'] ?? '');
$mensaje = trim($data['mensaje'] ?? '');

// Validación
if (empty($nombre) || strlen($nombre) < 3) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Nombre inválido.']);
  exit;
}
if (empty($institucion) || strlen($institucion) < 3) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Institución inválida.']);
  exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Correo electrónico inválido.']);
  exit;
}
if (empty($telefono) || strlen($telefono) < 7) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Teléfono inválido.']);
  exit;
}
if (empty($tipo)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Selecciona el tipo de institución.']);
  exit;
}
if (empty($mensaje) || strlen($mensaje) < 10) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'El mensaje es muy corto.']);
  exit;
}

$to = 'sistemas@grupomedibuy.com';
$subject = "Nueva solicitud de demo — $nombre";

$body = "
<html>
<body style='font-family:sans-serif;max-width:600px;margin:auto;padding:32px;background:#f8fafc;border-radius:12px;'>
  <h2 style='color:#1565c0;margin-bottom:4px;'>Nueva solicitud de demo</h2>
  <p style='color:#64748b;margin-top:0;'>Recibida desde enclaii.com</p>
  <hr style='border:none;border-top:1px solid #e2e8f0;margin:20px 0;'>
  <table style='width:100%;border-collapse:collapse;'>
    <tr><td style='padding:8px 0;color:#64748b;width:160px;'>Nombre</td><td style='padding:8px 0;color:#0f172a;font-weight:600;'>$nombre</td></tr>
    <tr><td style='padding:8px 0;color:#64748b;'>Institución</td><td style='padding:8px 0;color:#0f172a;font-weight:600;'>$institucion</td></tr>
    <tr><td style='padding:8px 0;color:#64748b;'>Correo</td><td style='padding:8px 0;color:#0f172a;font-weight:600;'>$email</td></tr>
    <tr><td style='padding:8px 0;color:#64748b;'>Teléfono</td><td style='padding:8px 0;color:#0f172a;font-weight:600;'>$telefono</td></tr>
    <tr><td style='padding:8px 0;color:#64748b;'>Tipo</td><td style='padding:8px 0;color:#0f172a;font-weight:600;'>$tipo</td></tr>
    <tr><td style='padding:8px 0;color:#64748b;vertical-align:top;'>Mensaje</td><td style='padding:8px 0;color:#0f172a;'>$mensaje</td></tr>
  </table>
  <hr style='border:none;border-top:1px solid #e2e8f0;margin:20px 0;'>
  <p style='color:#94a3b8;font-size:12px;text-align:center;'>ENCLAII — Sistema de Endoscopía</p>
</body>
</html>
";

$headers = [
  'MIME-Version: 1.0',
  'Content-Type: text/html; charset=UTF-8',
  'From: ENCLAII <sistemas@grupomedibuy.com>',
  'Reply-To: ' . $email,
];

$success = mail($to, $subject, $body, implode("\r\n", $headers));

if ($success) {
  echo json_encode(['ok' => true]);
} else {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'No se pudo enviar el correo. Intenta más tarde.']);
}
?>
