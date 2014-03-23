SERVERDIR=$(dirname $0)

php -S 0.0.0.0:80 $SERVERDIR/serve.php > $SERVERDIR/http.log 2>&1 &
HTTP_PID=$!

echo $HTTP_PID > $SERVERDIR/http.pid