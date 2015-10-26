def request(context, flow):
	flow.request.headers["x-requests-proxy"] = ["http"]

def response(context, flow):
	flow.response.headers["x-requests-proxied"] = ["http"]