Goals
=====

1. **Simple interface**

   Requests is designed to provide a simple, unified interface to making
   requests, regardless of what is available on the system.

2. **Fully tested code**

   Requests always has 90%+ code coverage from the unit tests and strives for
   100% coverage.

   (Note: some parts of the code are not covered by design. These sections are
   marked with `@codeCoverageIgnore` tags)

3. **Maximum compatibility**

   No matter what you have installed on your system, you should be able to run
   Requests. We use cURL if it's available, and fallback to sockets otherwise.
   (In fact, we should use more here. If you can think of any more transports,
   let us know and we'll add them!)

4. **No dependencies**

   Requests is designed to be entirely self-contained and doesn't require
   anything else at all. You can run Requests on an entirely stock PHP build.
