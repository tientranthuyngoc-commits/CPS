<?php
// Cấu hình tài khoản dùng để tạo QR chuyển khoản (demo đối phó, không qua cổng).
// Thay đổi theo tài khoản thật của bạn để demo.
return [
  // Mã ngân hàng theo img.vietqr.io (ví dụ: VCB, BIDV, TCB, MBB, ACB, VPB, VTBank: VTB ...)
  'bank_code'   => 'VCB',
  'account_no'  => '0123456789',
  'account_name'=> 'NGUYEN VAN A',
  // Mẫu hiển thị: compact, compact2, qr_only
  'template'    => 'compact2',
  // Tiền tố nội dung chuyển khoản
  'note_prefix' => 'ORDER',
];

