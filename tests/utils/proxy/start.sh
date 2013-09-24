PROXYDIR=$(dirname $0)

mitmdump -s proxy.py > $PROXYDIR/http.log &
HTTP_PID=$!

echo $HTTP_PID > $PROXYDIR/http.pid