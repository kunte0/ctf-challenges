Baby PHP
=======

# Author
kunte_

# Description
A chain of PHP quirks in one file: `php://input` to pass a `file_get_contents`
check, `intval($k1) !== $cc || $k1 === $cc`, a full-width `＄` to satisfy
`/^\d+＄/` while failing `is_numeric`, then variable variables (`$$lel`) to
overwrite the compared value and reach `system()`.

# Setup
apache + php5.6, see [setup.sh](src/setup.sh)

# Solution
see [checker.py](src/checker.py)

# Point Value
153

# Flag
flag{7c217708c5293a3264bb136ef1fadd6e}
