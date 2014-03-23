SERVERDIR=$(dirname $0)

cat $SERVERDIR/http.pid | xargs kill
rm $SERVERDIR/http.pid