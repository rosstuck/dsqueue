Intro
=====

This isn't intended for production. It's a proof-of-concept for a slightly
different take on a queue library. The idea is that it provides a way to write
abstraction layers for different queueing libraries without giving up much
flexibility. It's not done nor perfect.

The Parts
=========

There are 4 main interfaces in this lib. The tl;dr is "Jobs" are DTOs which
act as input for "Tasks" (essentially, service objects). You can put Jobs in
a "Queue" which acts as a backlog. Here's the tricky part: rather than pop'ing
jobs out of the queue, you instead pass in a "Consumer". The Consumer's job
is to match a Job to a Task, then run it. This double-dispatch gives the Queue
more control about the how/when of the execution and makes replacing or
decorating the consumer really easy.

Besides these interfaces, you also have a Worker class. This acts like a tiny
dispatcher, automatically processing your queue and firing off events for
various plugins to hook into.

The plugins themselves are very powerful and a number are already written. They
let you hook in via events and add your own strategies throughout execution.
You can find some examples in src/DS/Worker/Plugin directory.

For more information, check out the individual interfaces in the code; they
have lots of docs in them.

- [Jobs](./src/DS/Queue/Job/Job.php)
- [Task](./src/DS/Queue/Task/Task.php)
- [Queue](./src/DS/Queue/Queue.php)
- [Consumer](./src/DS/Queue/Consumer/Consumer.php)
- [Worker](./src/DS/Worker/Worker.php)

Todo
====
- Extract into an actual library, clear our the demo stuff
- Possibly rethink the SequentialJob layer. Maybe an actual Pipeline object somewhere?
- Write tests for the plugins
- Pass more info around with the Events?
- Rename Consumer?

Notes
=====
- The term "Consumer" came from the Rails queue API though it appears the
  meaning here is very different.

- Part of the inspiration for this approach came from PHP's Gearman extension.
  It relies on a callback based API which will segfault if you try to get the
  GearmanJob into a higher scope, thus rendering a push() method somewhat
  impractical (deserializing or cloning the job also leaves you without a clear
  way to update the job status on the server, bleh). Working around these
  limitations was one of the things that lead to this approach.