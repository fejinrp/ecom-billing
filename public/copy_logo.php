<?php
copy('/Users/chikku/Downloads/mtllogo.webp', __DIR__ . '/mtllogo.webp');
echo "Logo copied successfully!\n";
unlink(__file__);
