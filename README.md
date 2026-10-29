# Interval webGUI

This repository contains the editable web interface and its PHP mock backend for local testing.

Run the complete demo from this directory with:

```sh
php -S 127.0.0.1:8080 -t .
```

The firmware repository packages these files into `data/webapp/` through its
`preparefiles_MACOS.py` script. Set `WEBGUI_SRC` when the checkout is elsewhere.
