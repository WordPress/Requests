PROXYDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PORT=${PORT:-9000}

PROXYBIN=${PROXYBIN:-"$(which mitmdump)"}
ARGS="-s '$PROXYDIR/proxy.py' -p $PORT"
PIDFILE="$PROXYDIR/proxy.pid"

start-stop-daemon --start --background --pidfile $PIDFILE --make-pidfile --exec $PROXYBIN -- $ARGS
