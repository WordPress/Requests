PROXYDIR="$PWD/$(dirname $0)"

PIDFILE="$SERVERDIR/proxy.pid"

start-stop-daemon --stop --pidfile $PIDFILE --make-pidfile && rm $SERVERDIR/proxy.pid
