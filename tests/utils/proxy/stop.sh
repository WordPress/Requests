PROXYDIR=$(dirname $0)

cat $PROXYDIR/http.pid | xargs kill
rm $PROXYDIR/http.pid