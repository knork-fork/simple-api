### Profiling

Profiling is setup with `longxinH/xhprof`, but some kind of GUI needs to be installed to view the results.

`longxinH/xhprof` has a built-in GUI which can be run by running the following commands:

```bash
# optional, you can install xhprof into this repo
cd profiling/

git clone https://github.com/longxinH/xhprof.git
cd xhprof/xhprof_html && XHPROF_OUTPUT_DIR="$(realpath ../../../var/logs/xhprof)" php -S localhost:8181
```

Then, navigate to `http://localhost:8142` to view the results.

To view the profiling results, first run any route with `?profile=1`, e.g.:

```bash
curl --location 'localhost:11000/auth/token?profile=1' \
--header 'Content-Type: application/json' \
--data '{
    "secret": "password",
    "description": "token description",
    "expires": null,
    "is_read_only": false
}'
```