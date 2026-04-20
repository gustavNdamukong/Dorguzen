
LESSONS - UKPL SKELETON


-INTRODUCTION TO UKPL
-FUNDAMENTAL CONCEPTS 
      -SOLID Principles of Software 
        Development
	-Conventions & best practices
-Additional UKPLs for frameworks
-INTRODUCTION TO YOUR CHOSEN 
   LANGUAGE & SETUP




——————————————————————
    INTRODUCTION TO UKPL (VCDFT)
——————————————————————

  The Universal Keys to Programming Languages (UKPL) are 32 in number, and they fall under five (5) main groups. These five groups are as follows:


VACCCRF       V
—————
Variables, Arrays, Constants, Control flow, Commands, Regex, Functions
——————————————————————

CASSSS          C
—————
Cookies, Authentication, Sessions, Strings, Settings and configuration, Security
——————————————————————

DDDDDDPO      D
————-

Data types, Data structures, Design patterns, DB and Storage, Dates and Time, Frontend/UI and the URL, Package Management, OOP
——————————————————————

FFIIEENA         F
—————
File management, Forms and email, Images, Input/Output, Error Debugging and Testing, Extensions (APIs and Libraries), 
	Networking, Applications (in the eco-system)
——————————————————————

MTELSC          T
—————
Middleware, Templating, Event Handling, Localisation, Systems and Performance, Collaboration




——————————————————————
      FUNDAMENTAL CONCEPTS 
——————————————————————

    The SOLID Principles of Software 
      Development
    —————————————————
          S -Single Responsibility
          O -Open for Extension & closed for 
                Modification
           L -Likov's Substitution Principle.
                You should be able to substitute a 
                parent class with its child without 
                loosing any power or feature that 
                the parent would have provided. 
                Basically; children classes MUST 
                stay true to their parents.
           I -Interface Segregation Principle.
               Add interfaces to separate 
               functionality among your objects so 
               that it stays readable, less 
               confusing, and most importantly, 
               extendable (which equates to 
               loosely-coupled or scalable).
          D -Dependency Injection Principle. 
                Implement inversion of 
                Control (IoC) to make your 
                applications as loose-coupled as 
                possible.



    Conventions & best practices
    ———————————————
          -Always place a space after a 
            delimiter.
          -Place spaces on either side of binary 
            operators.
          -Create variable names in camel-
            casing
          -Create twig & config parameters in 
            snake-casing.
          -Declare class names beginning with
            uppercase & then use camel-casing.
          -Declare class members before 
            methods.
          -Declare class methods in this order:
            -First public methods at the top
            -Then protected methods
            -Lastly, private methods below
          -MVC design pattern conventions: 
              -never write SQL within a controller 
                or a view. 
              -All SQL should be in models.
              -DB table names should be all small 
                letters, & be in plural, while their 
                models are the singular of that-
                starting in uppercase eg table: 
                'users', model: 'User'.



——————————————————————
Additional UKPLs for frameworks
——————————————————————

Frameworks have the following additional group of keys:
Layout, Routing, Models, Views & Controllers (LRMVC)

                -REQUEST ROUTING IN DORGUZEN (DGZ)






        REQUEST ROUTING IN DORGUZEN (DGZ)
        ----------------------------------
    Dorguzen (DGZ) is a true MVC framework, and like most MVC systems, routing plays a key role in
directing HTTP requests to the appropriate controllers and actions. DGZ provides two routing
mechanisms that can be used independently or together:

1) Automatic Route Discovery
2) Defined Routes

Both approaches support regular controllers, module controllers, and API controllers.


1. Automatic Route Discovery
------------------------------

Automatic route discovery is DGZ's default routing mechanism. It maps incoming URLs to controllers
and methods by analysing the URI string — no manual route definitions needed.

1.1 How It Works

A typical DGZ request URI looks like this:

  Local:       http://localhost/yourapp/text1/text2/text3
  Production:  http://yourapp/text1/text2/text3

DGZ splits the URI into segments separated by slashes. It first determines the environment
(local or production), then skips the application-name segment and interprets the rest.

  Environment   Example URI                              Segments
  -----------   --------                                 --------
  Local         http://localhost/yourapp/contact/show/10  0:localhost 1:yourapp 2:contact 3:show 4:10
  Production    http://yourapp/contact/show/10            0:yourapp   1:contact 2:show    3:10


1.2 Segment-to-Controller Mapping

The first meaningful segment after the app name is the controller name.

  URI       → Controller looked up
  /contact  → ContactController   (found in src/controllers/)
  /about    → AboutController     (found in src/controllers/)
  /seo      → SeoController       (found in modules/seo/controllers/ — module route)

DGZ checks controller locations in this order:

  1. src/controllers/            — regular application controllers
  2. modules/{name}/controllers/ — module entry controllers (if the segment matches an active module)


1.3 Default Action

If the URI contains only the controller segment (e.g. /contact), DGZ calls the controller's
getDefaultAction() method automatically. All controllers must implement getDefaultAction().


1.4 Methods and Parameters

Additional URI segments are interpreted as either a method name or a parameter value.

  URI                       Action
  /about/show               Calls show() on AboutController
  /about/show/10            Calls show() with Request->targetId = 10
  /about/show/clothes       Calls clothes() on AboutController
  /about/show/clothes/4     Calls clothes() with Request->targetId = 4

Tip: intermediary segments can be omitted for cleaner URLs.
/about/clothes/4 works the same as /about/show/clothes/4.

This makes for readable, SEO-friendly URLs like:

  /employee/team-members/job-roles
  /employee/team-members/code-of-conduct


1.5 Module Sub-Controllers and Automatic Route Discovery

A module can have more than one controller. When automatic route discovery encounters a module
segment in the URL, it needs to know whether the next segment is a method on the module's default
(entry) controller, or the name of a different controller inside that module.

DGZ resolves this by calling getControllers() on the module's entry controller. This method
returns an array of all controller class names registered in that module. DGZ checks whether
the next URL segment matches one of those names and routes accordingly.

  URL: /seo/analytics/report
  → DGZ sees 'seo' is an active module
  → calls SeoController::getControllers()
  → finds 'AnalyticsController' in the list
  → routes to AnalyticsController::report()

  URL: /seo/refresh
  → DGZ sees 'seo' is an active module
  → calls SeoController::getControllers()
  → 'refresh' is not in the controller list
  → treats it as a method: calls SeoController::refresh()

To support this, a module's entry controller must implement DGZ_ModuleControllerInterface,
which requires one method:

  public function getControllers(): array;

There are two ways to fulfil this requirement:

  Option A — use DGZ_ModuleControllerTrait (recommended, least boilerplate):

      use Dorguzen\Core\DGZ_ModuleControllerInterface;
      use Dorguzen\Core\DGZ_ModuleControllerTrait;

      class SeoController extends DGZ_Controller implements DGZ_ModuleControllerInterface
      {
          use DGZ_ModuleControllerTrait;

          protected array $controllers = [
              'AnalyticsController',
              'ReportsController',
          ];
      }

      The trait provides getControllers() automatically, returning $this->controllers.

  Option B — implement the interface directly (more explicit):

      class SeoController extends DGZ_Controller implements DGZ_ModuleControllerInterface
      {
          protected array $controllers = ['AnalyticsController', 'ReportsController'];

          public function getControllers(): array
          {
              return $this->controllers;
          }
      }

IMPORTANT: A module with only one controller (its default entry controller) does NOT need
DGZ_ModuleControllerInterface or getControllers() at all. The router only calls getControllers()
when it needs to resolve a second URL segment that could be either a sub-controller or a method.
If you add sub-controllers to a module later, implementing the interface becomes mandatory —
without it, the router will throw a fatal 'method not found' error.


2. Defined Routes
-------------------

Defined routes give you explicit, full control over how requests are handled. They are declared
in route files and matched before automatic discovery is attempted — defined routes always
take priority.

All route files live in the routes/ directory:

  routes/web.php   — web (HTML) routes
  routes/api.php   — API routes


2.1 Web Routes

Web routes are defined in routes/web.php. Each route needs only two arguments:
  - the URI string
  - the action (ControllerName@methodName)

<?php
/** @var Dorguzen\Core\DGZ_Router $router */

$router->get('/contact',  'FeedbackController@defaultAction');
$router->get('/home',     'HomeController@home');
$router->post('/login',   'AuthController@login');

The URI is what a visitor types in the browser. The action names the controller and method
that will handle the request.

Supported HTTP verbs: get, post, put, patch, delete.

Route parameters:

$router->get('/shop/{id}', 'ShopController@show');

The {id} value is captured and available via request()->getTargetId() or $_REQUEST['targetId'].


2.2 Module Routes (Defined)

Module controllers — including non-default sub-controllers — work perfectly with defined routes.
Specify the module name as the third argument:

$router->get('/seo/refresh',             'Seo@defaultAction',    '', 'seo');
$router->get('/seo/analytics',           'Analytics@index',      '', 'seo');
$router->get('/seo/analytics/report',    'Analytics@report',     '', 'seo');

DGZ resolves 'Analytics@index' to modules/seo/controllers/AnalyticsController.php with
namespace Dorguzen\Modules\Seo\Controllers. The module name ('seo') provides the namespace
root; the controller name ('Analytics') identifies the class within it.

Note: when using defined routes for module sub-controllers, DGZ_ModuleControllerInterface
is NOT required. The controller is specified explicitly in the route definition, so there is
nothing to discover dynamically. The interface is only needed for automatic route discovery.


2.3 API Routes

API routes are defined in routes/api.php. They require a version number as the third argument,
and controllers live in src/api/{version}/controllers/:

<?php
/** @var Dorguzen\Core\DGZ_Router $router */

// POST /api/v1/auth/register  →  src/api/v1/controllers/AuthApiController.php
$router->apiPost('/api/v1/auth/register', 'AuthApi@register', 'v1');
$router->apiPost('/api/v1/auth/login',    'AuthApi@login',    'v1');

Supported API verbs: apiGet, apiPost, apiPut, apiPatch, apiDelete.

Arguments:
  1. URI string         (required) — always prefix with /api/v{n}/
  2. Action             (required) — ControllerName@method
  3. API version        (required) — e.g. 'v1'

It is strongly recommended to prefix all API routes with /api/ and the version number.
This makes the version explicit to API consumers and allows future versions to coexist cleanly.

Ensure the corresponding version directory (e.g. src/api/v1/controllers/) exists for each
supported version.

⚠️  .htaccess and URL depth

DGZ uses a single catch-all rewrite rule in .htaccess:

  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^ index.php [L]

This rule passes any request to index.php — unless the path maps to a real file or
directory on disk, in which case Apache serves it directly (static assets, uploads, etc.).

Because all routing logic lives inside DGZ_Router (which reads the full REQUEST_URI),
.htaccess does not need to know anything about URL depth. Routes of any length —
1 segment or 10 — are handled identically. This means:

  - Auto-discovery routes (/shop/products, /seo/analytics/report) work at any depth.
  - Defined routes (/api/v1/auth/login, /api/v1/docs/spec) work at any depth.
  - There is no "numbered rules" limit to hit.

If an API route (or any route) returns a bare Apache 404 — a plain HTML "Not Found"
page rather than a DGZ error view — it means the request never reached PHP. Check that
the catch-all rule is present and correct in .htaccess.


2.4 Route Caching

DGZ caches all registered routes to a flat PHP file on the first request after boot:

  storage/cache/routes.php

On subsequent requests the router loads that cached file directly, bypassing the route files
entirely for performance. This is transparent during normal development, but it becomes
important to know about in one specific situation:

⚠️  Whenever you add, remove, or change a route in routes/web.php or routes/api.php,
    delete the route cache file so the router picks up your changes:

      rm storage/cache/routes.php

    The cache is rebuilt automatically on the next request.

Symptoms of a stale route cache:
  - A new route returns a 404 or "No controller was found" error even though the
    controller file exists and the route is correctly defined.
  - Removing a route has no effect — requests still hit the old handler.

If you see either of those after editing a route file, clearing the cache is the
first thing to try.


2.5 Enforcing JWT Authentication on API Routes

Add DGZ_APITrait to any API controller that needs JWT validation:

  use Dorguzen\Core\DGZ_APITrait;

  class MyApiController extends DGZ_Controller
  {
      use DGZ_APITrait;

      public function protectedEndpoint(): void
      {
          $this->setHeaders();
          $tokenResponse = $this->validateToken();
          if (!$this->validatedToken) {
              $tokenResponse->send();
              exit();
          }
          // $this->validatedUser['user_id'] is now available
          ...
      }
  }

DGZ_APITrait provides: setHeaders(), validateToken(), refreshToken(), generateTokens(),
and refresh-token persistence helpers (saveRefreshToken, getRefreshToken, updateRefreshToken).


2.6 The JWT Secret Key
-----------------------

The JWT secret is set in your .env file:

  APP_JWT_SECRET=your-secret-here
  APP_JWT_ENCODING=HS256

This value is the private signing key used by the Firebase JWT PHP library to sign and
verify tokens. It does NOT come from the Firebase platform — you generate it yourself.
Any non-empty string will work technically, but for production you should use a strong
random value of at least 32 characters.

The easiest way to generate one is with openssl in your terminal:

  openssl rand -base64 48

Copy the output and paste it as your APP_JWT_SECRET value.

Important notes:

  - Never commit your real secret to Git. Keep it only in .env (which is in .gitignore).
    Use .env.example to document the key name with an empty or placeholder value.

  - If you rotate the secret (change it), all existing tokens are immediately invalidated —
    users will need to log in again. This is expected behaviour and is the correct way to
    revoke all active sessions at once if needed.

  - The placeholder value in the repo (xxxxxxxxxx...) is functional but weak. Replace it
    before going to production.


3. Hybrid Routing
-------------------

DGZ allows you to mix both approaches freely. Defined routes are checked first on every request.
If no defined route matches, automatic discovery takes over. This lets you use auto-discovery
for rapid development while adding explicit routes where you need fine-grained control,
middleware, or named routes.


Conclusion

Automatic route discovery is ideal for fast development — zero configuration, just create a
controller and visit its URL. Defined routes give you full control for production-quality apps,
APIs, and complex routing logic. Both approaches support regular controllers, module controllers,
and API controllers equally well.

Happy routing — the DGZ way.









——————————————————————
      INTRODUCTION TO YOUR CHOSEN
                LANGUAGE & SETUP
——————————————————————
  This section is for an introduction to the programming language of focus, with a roadmap and contents list with all the subheadings to give yourself a quick reference at a glance.
  The contents list should ideally be all the keys of the UKPL, while the sub-headings can be all the various areas that you will explore and learn about your chosen programming language, on that topic. For example, the content heading of Strings and its sub-headings can look something like this:

     -Strings
         -Syntax rules
         -Joining strings (concatenating)
         -Displaying a variable within a string
         -String functions

  This section should also contain instructions on how to setup an environment to start programming. This involves installation of the language and any vital dependencies needed, as well as system setup eg specifying the right operating systems and, or versions required. Also explain how to setup a development environment to allow you to start working in the programming language. These days you can hardly call yourself a programmer in a language if you do not understand how to setup up a system to be ready for development. Just provide for yourself a guide in steps on how to do the setup-going from the installation of dependencies (softwares and libraries), until writing the first line of code to display the famous ‘Hello world’ message on your computer screen.
——————————————————————



//------------------
VACCCRF 
//------------------
——————————————————————
	VARIABLES
——————————————————————

-A variable is like a virtual container in computer memory in which you can store things for later use in the runtime of your program. 
-It is performance-efficient to use them rather than to read from memory every time.
-You can only store one item in a variable at a time. This means an attempt to store an item in a variable that already contains something will result in the value of that variable being reset to the new value and the old value being discarded. Some programming languages will try to help you by erroring about the variable already having been set, but others will not and let you override the variable. You just have to be careful. 
-A variable can contain anything, ranging from a number, a string of text, a boolean, an array, a function, to even another variable (not commonly used). 





——————————————————————
	ARRAYS
——————————————————————

-An array is similar to a variable in that it is 
  like a virtual container in computer memory 
  to store things, with the only difference 
  that you can store multiple things at once.
-There are two types of arrays: 
    1) Numeric arrays aka index arrays
    2) Associative array

-Each element in the array is referenced by 
  a key which marks its spot in the array.  For 
  a numeric array, the key is a number (also 
  referred to as an index) and for an 
  associative array, that key is a small string 
  of text, also referred to as a name.
 -Here is an example of what the two array 
  types look like:

        1) Numeric arrays aka index arrays

            [
                [0] => 'John',
                [1] => 'Legend',
                [2] => 'jlegend@gmail.com'
            ]

            OR

          ['John', 'Legend', 'jlegend@gmail.com']
            
        2) Associative array

             [
                ['first_name'] => 'John',
                ['last_name'] => 'Legend',
                ['email'] => jlegend@gmail.com
            ]

  

   Array functions
   //———————-




——————————————————————
	CONSTANTS
——————————————————————

-A constant is also a virtual container in a   
  computer's memory in which you can store 
  a single element. 
-Just like a variable, it only stores one 
  element at a time.
-Where a constant differs from both 
  variables and arrays is that, once a 
  constant has been defined with its value, 
  that value cannot be changed throughout 
  the lifespan of your program.




——————————————————————
	CONTROL FLOW (OCLS)
——————————————————————

-Control flow in computer programming 
  refers to ways in which the behaviour of 
  your program can change based on the
  result of an expression, or the value of a 
  dynamic variable.
-This is at the core of how intelligent 
  computer programs like algorithms, and 
  even modern artificial intelligence (AI) 
  systems are written.


-OCLS
    Operators
    Conditionals
    Loops
    Switch/case statements






——————————————————————
	COMMANDS
——————————————————————

              -Commands vs Events — what is the difference?
              -The Dorguzen Task Scheduler
                  -How It Works
                  -Commands
                  -Jobs
                  -Example full setup
                  -Other Examples
                  -Final Summary
              -RABBITMQ QUEUE DRIVER
                  -Installation
                      -What is RabbitMQ?
                      -Is this the same RabbitMQ corporations use?
                      -How does PHP talk to RabbitMQ?
                      -Should it be installed on demand or shipped with Dorguzen?
                      -The Management UI
                      -Step-by-step: the quickest way to get RabbitMQ running and test it
                  -Configuration
                  -How the driver works
                  -Attempt Tracking
                  -Delayed Jobs
                  -Running workers against RabbitMQ
                  -Monitoring
                  -Switching Drivers





  This is a piece of code that you write to run behind the scenes immediately or at schedule times to complete tasks.
This is used in asynchronous programs and things like cron jobs.


  Commands vs Events — what is the difference?
  ---------------------------------------------

  A common point of confusion is when to reach for a Command (queued job /
  scheduled task) versus an Event (observer pattern). They look similar on
  the surface — both can trigger code asynchronously — but they model
  fundamentally different things.

  EVENT HANDLING is about the observer pattern. An event says "something
  happened" and any number of listeners react to it. The event itself does
  not care who is listening or what they do. Example: a user registers →
  listeners send a welcome email, log the action, update analytics. The
  event is the notification; the listeners are the reactions.

  COMMANDS (jobs and scheduled tasks) are about work. A command says "do
  this thing" — process a payment, resize an image, send a batch of
  newsletters. Commands are dispatched to a queue driver (sync, database, or
  RabbitMQ) and consumed by a worker at the right time.

  The queue system is a transport layer that events *can* use (a listener
  that implements ShouldQueue is handed to the queue driver), but the queue
  itself is not an event concept. Putting the queue driver docs in EVENT
  HANDLING would be like putting a postal truck manual inside a chapter
  about letters — the truck delivers the letters, but it belongs in a
  different chapter.

  The right structure in Dorguzen is:

      COMMANDS
        ├── The Dorguzen Task Scheduler   (scheduling when work runs)
        └── RabbitMQ Queue Driver         (where queued work is stored/consumed)

      EVENT HANDLING
        └── Built-in Dorguzen Core Events (observer pattern — fire and react)

  When in doubt: if you are modelling something that *happened*, use an
  Event. If you are modelling work that *needs to be done*, use a Job/Command.


                

                



        THE DORGUZEN TASK SCHEDULER 
        ---------------------------- 

  The DGZ Task Scheduler is Dorguzen's time-based automation engine.

It allows developers to define scheduled:

Commands
Jobs
Events

Using expressive methods such as:

$schedule->command('app:test')->everyMinute();
$schedule->job(MyJob::class)->dailyAt('09:30');
$schedule->event(MyEvent::class)->withoutOverlapping();

The scheduler is:

Cron-driven

Database-safe

Overlap-aware

Framework-integrated

Fully decoupled from business logic

🧠 Architectural Philosophy

DGZ follows a strict separation of concerns.

Component	Responsibility
Schedule	Developer task definitions
ScheduleLoader	Loads user schedule file
ScheduleRunCommand	Orchestrates scheduler run
Scheduler	Executes due tasks
ScheduledTask	Represents one scheduled task
SchedulerLock	Prevents overlapping execution
scheduled_task_locks	Database-level locking

The scheduler:

Does not contain business logic

Does not perform long-running loops

Does not depend on Redis

Does not require daemon mode

It simply answers:

“Is this task due now?”
“If yes, execute it safely.”

⚙️ How It Works Internally
1️⃣ Developer Defines Tasks

File:

      src/CLI/Console/Schedule.php

Example:

      return function (Schedule $schedule): void {

          $schedule->command('app:test')
                  ->everyMinute();

          $schedule->job(\App\Jobs\CleanupJob::class)
                  ->dailyAt('09:30');

          $schedule->event(\App\Events\ReportEvent::class)
                  ->everyMinute()
                  ->withoutOverlapping();
      };

This file does not execute anything.
It only defines tasks.

2️⃣  Cron Triggers the Scheduler

In production:

      * * * * * php /path/to/project/dgz schedule:run

This runs once per minute.

DGZ does not daemonize the scheduler.
Cron is the trigger.

3️⃣  schedule:run Command

The command:

      php dgz schedule:run

It:

Loads the developer schedule file

Creates a Schedule object

Iterates through tasks

Checks if each task isDue()

Hands due tasks to Scheduler

4️⃣  The Scheduler (Execution Brain)

File:

      core/Console/Scheduling/Scheduler.php

Responsibilities:

Enforce overlap prevention

Dispatch commands

Dispatch jobs

Fire events

Release locks safely

It uses:

      match ($task->getType()) {
          'command' => $this->runCommand($task),
          'job'     => $this->runJob($task),
          'event'   => $this->runEvent($task),
      };

It wraps execution in:

      try {
        ...
      } finally {
        $this->lock->release(...)
      }

This guarantees:

No orphaned locks

No stuck tasks

Safe failure handling

🗂 ScheduledTask

Each task internally stores:

Type (command, job, event)

Target (string or class)

Cron expression

Overlap setting

Example internal representation:

  command → app:test
  cron    → * * * * *

It contains:

      withoutOverlapping()
      preventsOverlapping()
      lockKey()
      isDue(DateTime $now)


⏱ Cron Expressions
--------------------

DGZ uses standard 5-field cron format:

      * * * * *
      │ │ │ │ │
      │ │ │ │ └── Day of week (0-6) eg Monday
      │ │ │ └──── Month (1-12)
      │ │ └────── Day of month (1-31) eg 25
      │ └──────── Hour (0-23)
      └────────── Minute (0-59)

Examples:

      Expression	    Meaning
      * * * * *	      Every minute
      0 * * * *	      Every hour
      0 0 * * *	      Daily at midnight
      0 0 1 * *	      Monthly on 1st
      30 9 * * *	    Daily at 09:30

Helper methods like:

      everyMinute()
      daily()
      dailyAt('09:30')
      weekly()

generate cron expressions automatically.

🔒 Overlap Prevention
Why It Exists

Without overlap prevention:

Slow tasks could be scheduled again

Duplicate executions could occur

Data corruption risks increase

DGZ solves this with:

      ->withoutOverlapping()

Database Locking Strategy

Table:

      CREATE TABLE scheduled_task_locks (
          task_key VARCHAR(255) PRIMARY KEY,
          locked_at DATETIME NOT NULL,
          expires_at DATETIME NOT NULL
      );


How It Works
----------------

Scheduler attempts to INSERT

If insert succeeds → lock acquired

If duplicate key error → skip execution

Lock released in finally block

No Redis required.
No race conditions.
Pure DB atomicity.


🚀 What Can Be Scheduled?



Commands
---------

$schedule->command('queue:work')->everyMinute();

Internally executed using Symfony Console:

      $app->getConsole()->run(
          new ArrayInput(['command' => 'queue:work']),
          new NullOutput()
      );



Jobs
--------
$schedule->job(MyJob::class)->daily();

Dispatches to DGZ queue system:

dispatch(new MyJob());
Events
$schedule->event(MyEvent::class)->everyMinute();

Triggers event system:

event(new MyEvent());

Listeners may:

Run immediately

Or be queued

🔄 Expected Workflow

Cron runs scheduler every minute

Scheduler checks due tasks

Commands run immediately

Events fire immediately

Jobs get queued

Queue worker consumes jobs

Typical production setup:

* * * * * php /path/dgz schedule:run

Queue worker (running continuously):

dgz queue:work



Why Use a Scheduler?

Without it:

You must manually dispatch tasks

No time-based automation

No central orchestration

With it:

Time-driven automation

Centralized scheduling

Overlap protection

Clean architecture

The scheduler answers:

WHEN should something run?

The queue answers:

HOW should it run?

The job answers:



WHAT should run?


🛡 Reliability Guarantees (v1)

Safe DB locks

No daemon complexity

Cron-driven simplicity

Try/finally lock release

Cross-platform CLI

Clean separation of concerns

DGZ Scheduler Status

As declared:

✅ DGZ Task Scheduler is complete for Version 1

Future (optional enhancements):

Execution history table

Failure tracking

Stale lock pruning

Long-running scheduler mode

Dashboard insights

None are required for v1 stability.



  Example Full Setup

1️⃣ Define tasks
$schedule->command('emails:send')->everyMinute();
$schedule->job(CleanupJob::class)->dailyAt('02:00');
$schedule->event(ReportGenerated::class)->weekly();


2️⃣  Add cron
* * * * * php /path/to/project/dgz schedule:run


3️⃣  Run queue worker
dgz queue:work

Done.



Other examples
--------------------
$schedule = new \Dorguzen\Core\Console\Scheduling\Schedule();

(require base_path('src/CLI/console/Schedule.php'))($schedule);

dump('Scheduled tasks', $schedule->getTasks());

foreach ($schedule->getTasks() as $task) {
    dump($task->getType().' | '.$task->getTarget().' | '.$task->getExpression()?->value());
}



🎯 Final Summary
The DGZ Task Scheduler:
-----------------

Is cron-driven
Is database-safe
Prevents overlap
Supports commands, jobs, events
Integrates seamlessly with the queue system
Requires zero external services

Here is the architectural structure and list of some key classes involved:
    rc/CLI/console/Schedule.php containing the Schedule class. 

    The Schedule class contains the user-defined scheduled tasks.

    Tasks that can be defined are commands, jobs, and events.
      This makes it so powerful because it consolidates the power of 
      Dorguzen's other sub-systems:
        -The Events system 
        -The Queue system 
        -Console commands. 
      all of which can be scheduled to automatically run at specific dates and times decided by you. 

  The commands, jobs and events have to be wrapped into concrete classes to be run by the scheduler.
  These concrete classes are three in number, which we can refer to as the 'schedule type' classes:

      -ScheduledCommand 
      -ScheduledJob 
      -ScheduledEvent

  ScheduledTask is a class that defines the contract to be folowed by the scehdule type (concrete) classes.
  ScheduleRunner is a class that runs the schedule type classes.
  CronExpression defined in CronExpression.php defines the timings (date/times) for tasks to be run.





  RABBITMQ QUEUE DRIVER
  ======================

  The Dorguzen queue system is driver-based. The active driver is set via one
  environment variable:

      QUEUE_DRIVER=rabbitmq   # .env

  Three drivers ship with Dorguzen:

  Driver       | When to use
  ------------ | -----------------------------------------------------------
  sync         | Development / simple apps. Jobs run inline, no worker needed.
  db           | Production without a message broker. Jobs stored in dgz_jobs.
  rabbitmq     | Production with high throughput / fan-out needs. Jobs sent to
               | a RabbitMQ broker and consumed by one or more workers.


  1️⃣  Installation
  -----------------

  What is RabbitMQ?
  -----------------
  RabbitMQ is an open-source message broker — a piece of server software that
  sits between your application and your background workers, reliably storing
  messages (jobs) and delivering them to consumers. It implements the AMQP
  protocol (Advanced Message Queuing Protocol 0-9-1), which is the same
  standard used by banks, telecoms, and large internet platforms.

  It is NOT a JDK or Java application. RabbitMQ is written in Erlang/OTP —
  a language built specifically for massively concurrent, fault-tolerant
  systems. You do not need to know Erlang to use it; the Erlang runtime is
  simply a dependency that RabbitMQ runs on, in the same way that a PHP
  application runs on the PHP interpreter.


  Is this the same RabbitMQ corporations use?
  --------------------------------------------
  Yes — exactly the same software. There is no "lite" or "developer" edition.
  RabbitMQ is open-source (Mozilla Public License) and the same binary that
  runs on a developer's laptop scales to handle millions of messages per second
  in production. Companies like Instagram, Reddit, and countless enterprises
  run it in production. The version you install locally is identical to what
  they run in their data centres.


  How does PHP talk to RabbitMQ?
  ------------------------------
  PHP communicates with RabbitMQ over a TCP socket using the AMQP wire
  protocol. There are two types of PHP AMQP clients available:

    1. php-amqplib (pure PHP) — implemented entirely in PHP; nothing extra
       to install beyond the Composer package. This is what Dorguzen uses.

    2. php-amqp (C extension) — a compiled PHP extension, faster under heavy
       load, but requires installing and configuring an extension on the
       server. Not required here.

  Because Dorguzen uses php-amqplib (pure PHP), there are no PHP extensions
  to configure. It works wherever Composer runs.


  Should it be installed on demand or shipped with Dorguzen?
  ----------------------------------------------------------
  On demand only, for two reasons:

    1. RabbitMQ is a separate server process (a system service, like MySQL or
       Redis). It cannot be bundled into a Composer package — it must be
       installed on the machine or run via Docker. Dorguzen cannot ship it.

    2. Most applications do not need it. QUEUE_DRIVER=sync handles the
       majority of use cases. Only reach for RabbitMQ when you need
       distributed workers, high throughput, or fan-out across multiple
       services.

  Dorguzen protects you with a clear error if you set QUEUE_DRIVER=rabbitmq
  before the broker or the PHP client library is in place:

      "RabbitMQ driver requires php-amqplib.
       Install it with: composer require php-amqplib/php-amqplib"



  The Management UI
  -----------------
  RabbitMQ ships with an optional web-based management interface that gives
  you a live dashboard of your queues — message counts, publish/consume
  rates, connected consumers, dead-lettered messages, and the ability to
  manually publish or purge messages.

  Enable it after installing RabbitMQ:

      rabbitmq-plugins enable rabbitmq_management

  Then visit:  http://localhost:15672   (default login: guest / guest)

  The UI is invaluable when debugging. You can watch jobs appear in the queue
  when dispatched, and disappear the moment a worker acknowledges them.






  Step-by-step: the quickest way to get RabbitMQ running and test it
  -------------------------------------------------------------------


  OPTION A — Docker (recommended, zero system footprint)
  -------------------------------------------------------
  This single command pulls the official RabbitMQ image with the management
  UI already enabled — no Erlang install, no system service, nothing else:

      docker run -d --name rabbitmq \
        -p 5672:5672 \
        -p 15672:15672 \
        rabbitmq:3-management

  That single command is all you need. No Dockerfile required. Here is what that command does:

      1. Docker Desktop checks if rabbitmq:3-management exists    
          locally                                                                                                          
      2. It doesn't yet — so Docker automatically pulls it from Docker Hub (the official RabbitMQ image, maintained by 
      
          the RabbitMQ team)

      3. It starts the container in the background 
          (-d)                                                                                                                         
      4. Port 5672 on your machine → AMQP port inside the container (what php-amqplib connects to)

      5. Port 15672 on your machine → Management UI inside the 
          container                                                                                                        
                                                                                                                                                                            
  That's it. By the time the command returns to your prompt, RabbitMQ is running. 
  Visit http://localhost:15672 and you'll see the login page immediately.                   
                                                                                                                                                                            
  The -management tag is the key detail — the official image comes in two flavours:                                                                                         
                                                            
  ┌───────────────────────┬────────────────────────────────────────┐                                                                                                        
  │       Image tag       │              What you get              │
  ├───────────────────────┼────────────────────────────────────────┤                                                                                                        
  │ rabbitmq:3            │ Broker only, no UI                     │
  ├───────────────────────┼────────────────────────────────────────┤
  │ rabbitmq:3-management │ Broker + management UI already enabled │                                                                                                        
  └───────────────────────┴────────────────────────────────────────┘
                                                                                                                                                                            
  You want the -management one for development so you can watch the queues visually.                                                                                        
   
  The only prerequisite is that Docker Desktop is open and running — that's it. 
  No Erlang install, no system service, no config files, nothing else.                        

  RabbitMQ is now running. Visit http://localhost:15672 (guest / guest).
  Stop and remove it any time with:  
  
      docker stop rabbitmq && docker rm rabbitmq



  OPTION B — Homebrew (macOS)
  ----------------------------
      brew install rabbitmq
      brew services start rabbitmq
      rabbitmq-plugins enable rabbitmq_management



  OPTION C — Ubuntu / Debian
  ---------------------------
      sudo apt install rabbitmq-server
      sudo systemctl start rabbitmq-server
      sudo rabbitmq-plugins enable rabbitmq_management

  Once the broker is running, wire Dorguzen to it:

      Step 1 — Install the PHP client library:

          composer require php-amqplib/php-amqplib

      Step 2 — Update .env:

          QUEUE_DRIVER=rabbitmq
          RABBITMQ_HOST=127.0.0.1
          RABBITMQ_PORT=5672
          RABBITMQ_USER=guest
          RABBITMQ_PASSWORD=guest
          RABBITMQ_VHOST=/
          RABBITMQ_QUEUE=default

      Step 3 — Dispatch any job from your application:

          dispatch(new MyJob());

      Step 4 — Start a worker:

          php dgz queue:work

      Step 5 — Open the management UI at http://localhost:15672 and go to the
      Queues tab. You will see the "default" queue with your job sitting in it,
      then watch it disappear as the worker picks it up and acknowledges it.

  That is all it takes. The same pattern works identically in staging and
  production — just point RABBITMQ_HOST at your remote broker.



  2️⃣  Configuration
  -------------------

  Add these keys to your .env (all have safe defaults for local development):

      QUEUE_DRIVER=rabbitmq

      RABBITMQ_HOST=127.0.0.1   # RabbitMQ broker hostname or IP
      RABBITMQ_PORT=5672         # Default AMQP port
      RABBITMQ_USER=guest        # Broker username
      RABBITMQ_PASSWORD=guest    # Broker password
      RABBITMQ_VHOST=/           # Virtual host (default '/')
      RABBITMQ_QUEUE=default     # Queue name

  The values are consumed by configs/app.php under the 'rabbitmq' key and
  read by QueueManager when it instantiates RabbitMQQueue.

  You do NOT need to pre-create the queues in the RabbitMQ management UI.
  The driver declares them automatically on first use (durable, so they
  survive broker restarts).


  3️⃣  How the driver works
  --------------------------

  RabbitMQQueue implements the same QueueInterface as DatabaseQueue and
  SyncQueue, so every queue feature — delayed jobs, retries, failure logging,
  poison-job protection — works identically regardless of which driver is
  active. The underlying AMQP operations are:

  QueueInterface method  →  AMQP operation
  -----------------------    -------------------------------------------------
  push()                 →   basic_publish to the main queue
  pop()                  →   basic_get (non-blocking poll); returns null when empty
  acknowledge()          →   basic_ack — job succeeded, remove it from the broker
  fail()                 →   basic_nack (no-requeue) + basic_publish to <queue>.failed
  release()              →   basic_ack original + basic_publish new copy (incremented attempts)
  stats()                →   queue_declare (passive) on main queue + failed queue
  clear()                →   queue_purge on main queue

  Two queues are maintained automatically:

      default           — active jobs waiting to be processed
      default.failed    — jobs that exceeded max_attempts (for inspection / replay)

  (The name 'default' matches your RABBITMQ_QUEUE setting.)


  Attempt tracking
  ----------------
  RabbitMQ cannot modify an in-flight message's body. Dorguzen stores attempt
  metadata in AMQP application_headers (an AMQPTable attached to each message):

      attempts      — how many times this job has been attempted
      max_attempts  — ceiling (default 3, driven by QueuedJob)
      available_at  — Unix timestamp; job is skipped by pop() until this passes

  On release() the original message is ack'd (consumed) and a brand-new
  message is published with the incremented attempts value in its headers.
  This is functionally equivalent to the UPDATE dgz_jobs SET attempts = ?
  approach used by DatabaseQueue.


  Delayed jobs
  ------------
  Delayed dispatch works without any RabbitMQ plugins:

      $queue->push(new SendReminderJob($user), delaySeconds: 3600); // 1 hour

  The available_at timestamp is stored in headers. pop() checks it on each
  poll and nack's (with requeue=true) any message that is not yet ready. The
  message stays safely in the queue and will be picked up by the next worker
  poll after the delay has elapsed.

  Note: For very precise large-scale delay scheduling (millions of delayed
  messages), consider the rabbitmq-delayed-message-exchange plugin instead.
  The header-based approach above is simpler and works well for typical use.


  4️⃣  Running workers against RabbitMQ
  ---------------------------------------

  The worker command is the same regardless of driver:

      php dgz queue:work

  With QUEUE_DRIVER=rabbitmq the worker calls RabbitMQQueue::pop() on each
  loop iteration. basic_get is non-blocking — if the queue is empty it
  returns null immediately and the loop sleeps briefly before trying again.

  Multiple workers can safely run in parallel. RabbitMQ's broker-side
  delivery guarantees ensure each message is delivered to exactly one worker.


  5️⃣  Monitoring
  ----------------

  Check queue depths from the CLI:

      php dgz queue:jobs

  Internally this calls QueueManager::stats(), which the RabbitMQ driver
  fulfils by running queue_declare (passive=true) — a read-only probe that
  returns the current message count without touching the queue.

  You can also use the RabbitMQ Management UI (http://localhost:15672 when
  the management plugin is enabled) to inspect queues, consumers, and
  message rates visually.


  6️⃣  Switching drivers
  -----------------------

  Because QueueManager is the only entry point and all drivers implement the
  same interface, you can switch from 'db' to 'rabbitmq' (or back) with a
  single .env change:

      QUEUE_DRIVER=db        # uses dgz_jobs table
      QUEUE_DRIVER=rabbitmq  # uses RabbitMQ broker

  No application code changes required.






——————————————————————
	REGEX
——————————————————————

Intro
Features
Grouping
Classes
Range
Negation
Anchor characters
Shorthand meta characters
Quantifiers
Modifiers
Regular expressions in {languageHere}
Practical examples and solutions



A regular expression (regex) is a 
  sequence of characters that forms a 
  search pattern. It is used for string 
  matching, searching, and manipulation by 
  defining specific patterns that help identify 
  and extract parts of a string.
 
  Here are its features:
      -i) Pattern Matching: 
         Regular expressions allow you to 
         define patterns that match specific 
         sequences in text. For example, finding 
         email addresses, phone numbers, or 
         validating formats like dates.
     -ii) Search and Replace**: Regex can be 
         used to find occurrences of patterns in 
         text and replace them with new strings.
     -iii) Flexible and Powerful**: It provides a 
         flexible way to define complex string 
         patterns using literals, wildcards, 
         quantifiers, and special characters.

    Some common use cases for regular 
  expressions are:
      -Validation: To validate email addresses, 
          URLs, phone numbers, etc.
      -Text Search: To find specific patterns 
          within large text files.
      -String Manipulation: To extract or 
           replace certain parts of a string, like 
           removing extra spaces or converting 
           formats.

    Regular expressions are a powerful tool 
  for working with strings in various 
  programming languages, including PHP, 
  Python, JavaScript, and many others. They 
  are widely used in text processing tasks for 
  pattern matching and validation.
    Here are some of the most popular
  characters available:

              *   .   +   []   ()   {}

    With regular expressions, you can 
  construct a powerful pattern-matching 
  algorithm using just a single expression.
    The syntax is simple, it starts with a 
  forward slash and ends with a forward 
  slash. You have to place the pattern to be 
  matched between them. The pattern is 
  made up of special characters called meta 
  characters. 
      We will proceed by listing the patterns 
  with their meanings, and separate them 
  under topics according to their behaviours.
 
  /*/        An asterisk character. It matches 
              any number of characters or 
              nothing

/./          A dot character. It matches all kinds 
             of characters except a newline (\n).    
             It has a limitation, however; it will 
             match only a single character, so /./ 
             will match everything on the page or 
             subject string by singling them out 
             one by one. That means, if you use it 
             on a string of characters, it will 
             match and stop at the first 
             character. That is why it is better to 
             use it in a combination with for 
             example + or * to expand its 
            capability.

/+/        Matches at least one or more 
            characters. It is therefore often used    
            in a combination. Unlike the * 
            (asterisk) character, it does not 
            match nothing-as in, if no character 
            appears in the subject string, it will 
            not match.

/<.>/    It will match anything that comes 
            between '<' and '>' characters. This 
            will match the beginning names of an 
            HTML tag. It wont match blank tags 
            (<>). Another limitation which we 
            already know is; it will match only 
            tags with single letters. For example, 
            it will match <p> and <b> but will not 
            match <em>.

/<.*>/   Expends the above pattern and fixing 
            its limitations. It will match blank 
            tags, and also tags with multiple 
            letters. This is because the 
            *(asterisk) character can also  
            match nothing as well as anything. 
            Combining meta characters like this 
            therefore is useful because they are 
            all combined and executed. Your aim 
            is to have as intelligent an app as 
            possible that will not leave potential 
            matches unmatched. The effort to 
            match as much as possible is known 
            as 'fuzzy character matching'.

/<.+>/  It can be used instead of /<.*>/ if you
            do not want to match blank tags
            (<>). This + character has a 
            limitation however. In this case, it will 
            keep matching till the last '>' on the 
            line of text. This means that you may 
            end up with a match like this:

                 <p><b>Hello</b></p>

            This is not ideal, and not what you 
            may want. There is a better way to 
            use the + character. Here it is:

                  /<[^>]+>/

            This tells the system to match an 
            opening tag (<) then ignore any 
            number of closing tags (>) that come 
            after that, until (except) the last '>' 
            character. 
               Note that the […] and the + 
           characters are a combination.

/?/      Matches an element zero or only 1 
          time. It is used to make matches on a 
          specific element optional in the 
          subject string.

    If you ever need to literally match a 
  character which happens to be regex meta 
  character, you can do that by simply 
  placing a black slash in front of the 
  character. This k own as escaping the 
  character so that your code parser does 
  not treat the character as a regex pattern 
  to use for matching, but to match the 
  character in the subject string literally as it 
  is given. For example, to match the digits 
  5.0, you need to escape the dot (.) in it like 
  so: 

                /5\.0/

  You may not be sure of how many 
  characters or digits will come after the dot 
  character. For example it could be 5.0, or 
  5.00 or 5. 000 etc. The solution to this is to 
  simply add an asterisk after the last zero 
  like so

           /5\.0*/

  The back slash can escape anything 
  including a back slash itself, which is useful 
  in case there is a back slash in the string 
  you are matching.  
    Note that there are some so-called 
  shorthand matching characters that start 
  with a back slash. I will provide a list of 
  these later below.

  In regular expressions, there are the 
  following concepts:
   -grouping
   -classes
   -ranges

         GROUPING
        ——————-
  Grouping is done with a combination of a 
  pair of parentheses followed by a meta 
  character like + or *
  The parentheses enclose the thing in the 
  subject string to be matched, while the 
  meta characters after the parentheses tell 
  the system how many times to group the 
  matched string. The matched string could 
  be a string of numbers. Here is an 
  example:

          /1(,000)+ /  will match any of these:
             -1,000
             -1,000, 000 etc

  Keep a space between the + character and
  the last '/' character for better matching. 
  You would then be able to match all digits 
  like 1,000, 000 or 1,000, 000, 000 etc. 
  Otherwise it will only be a match if the 
  grouped digits ',000' have no space 
  between them like this:
  
       1,000,000 


              CLASSES
              —————
    A class is simply a pair of square brackets 
  with the string to be matched between 
  them. There will be a successful match if 
  any of the characters in the bracket is 
  found in the subject string. For example:

/gr[ae]y/    This will match 'gray' 'grey'. Any 
                   meta characters after the square 
                   brackets will tell the system to 
                   repeat that match any number of 
                   times. If you modify the pattern 
                   like /gr[ae]+y/ then it will match 
                   'greey' and 'graay' and 'greay' 
                   and 'graey'. Just understand that 
                   without the + character adter the 
                   square brackets, each character 
                   between the brackets will be 
                   matched only once.

    An alternative way to achieve the same 
  out come is to use a pipe character like so:
  /a|e/     You simply place a pipe character 
               between the characters you want 
               to match either of. This approach is 
               not a class, but i am showing you 
               here just for informative purposes.


           RANGE

              A range will match any character 
              within a range.
/[0-9]/   will match any number between 0 
              and 9. Just place a hyphen between 
              the two numbers. [5-8] will match 
              any number between 5 and 8.

/\d/        Is a shorthand to match any single 
             digit number. It's exactly the 
             equivalent if the pattern above (/ 
             [0-9]/



          NEGATION
    You use this to create a pattern that the
  match must not be. Basically, you are 
  saying g that a match should be everything 
  but not this negation pattern. You create a 
  negation pattern by placing a caret 
  character as the first thing inside a pair of 
  square brackets. Every other character 
  that follows the caret character i side the  
  brackets is a negation pattern.
    If the square bracket is followed by a 
  meta characters eg eg +  or *, then the 
  square bracket and the meta character are 
  a combination.



       ANCHOR CHARACTERS (^ and $)
       —————————————————-

    Outside of the use of square brackets, 
  there are two other characters:

       -the 'anchor' (to establish start position 
           of) the search string, for a match to 
           occur. The anchor is made up of both 
           the caret (^) and the ($) characters.

  Without using squares brackets 
  (containing the ^ character at the start), 
  the pattern will be a negation character. 
  If the caret character 
  appears at the start of your regex pattern, 
  then the text to be matched must be at the 
  start of the subject text for a match to 
  occur.
    On the other hand, if the $ character is 
  placed at the end of your regex, then the 
  string being matched must be at the end of 
  the line of text for a match to be made. For 
  example, to match a subject string that 
  has the text:
    “Le Guin” and nothing else, just anchor 
  the two ends to make sure our text starts 
  and finishes the line like so:

         /Le  *Guin$/  


        Shorthand meta characters
       ———————————————
  These are shorthand characters for 
  frequently used characters, built into 
  regexes. 

/|b/         matches a word boundary

/|B/        matches no word boundary 

/ \d/      single digit

/ \D/     single non-digit

/ \n/     newline character 

/ \s/     white space character

/ \S/    non-white space character

/ \t/     tab character 

/ \w/    word characters (a-zA-Z0-9_)

/ \W/   non-word character, so anything but 
            any of these:  a-z, A-Z, 0-9 and _



          Quantifiers
        ————————
    In regular expression (regex) terminology, 
  the curly braces {} are referred to as 
  quantifiers. They are used to specify the 
  exact number of occurrences, or a range 
  of occurrences, of a preceding element.
    You can use a quantifier in 3 ways:

  -i) /[\w]{3}/  with ONE digit. This means you 
                       wish to match word character 
                       if it appears thrice.

  -ii) {3,}     with ONE digit and a comma. 
                   This means you wish to match 
                   the preceding character if it 
                   appears 3 or more times.
  -iii) {2, 3}  with TWO digits separated by a 
                   comma. This means you wish to 
                   match the preceding character if 
                   it appears any number of times 
                   between two or 3.


          Modifiers
        ——————-
    These are special characters that you 
  place outside, but at the end of a regex 
  pattern to influence how the complete 
  match will be made. There are three (3) 
  modifiers, global (g), case-insensitive (i), 
  and multiline (m). Let's dive straight into 
  some examples, assuming that 
  '/…/' contains your regex pattern:

  /…/g      This will match in a global manner, 
                rather than at the first match that 
                is encountered. You could end up 
                with multiple matches rather than 
                just one. Say you are matching any 
                occurrences of the word 'cat' in a 
                subject string, if the string had 4 
                occurrences of the word 'cat', it will 
                return all 4 occurrences rather 
                than just 1 for the first one.

/…/i        This makes the match case-
               insensitive. Matches are normally 
               case-sensitive, so having this will 
               make the following regex patterns 
               work in the same way:
          
                 /[a-zA-Z/    and    /[a-z/i

                 or

                /A-Z/      and    /[a-z]i


/…/m      This enables multiline mode, 
               meaning that the match will be 
               made across multiple lines of text.





       Regular expressions in {languageHere}
      ——————————————-
    This is a placeholder for you to fill in with 
  notes on how your preferred programming 
  language handles regular expressions.





      Practical examples and solutions
       ————————————————
-1) Write a regex pattern to match a social 
      security number format like:

             123-45-6789

     Solution:

            ^\d{3}-\d{2}-\d{4}$

     Explanation:

         -The '^' and '$' anchors ensure the 
           pattern starts and ends correctly.
         -\d{3} matches exactly 3 digits before 
           the first hyphen.
         -Next, we literally match the hyphen.
         -\d{2} matches the next 2 digits.
         -\d{4} finally matches the last 4 digits.

         In PHP you can use preg_match() to 
         match a regular expression:

           $pattern = "/\d{3}-\d{2}-\d{4}/";
           $string = "My SSN is 123-45-6789";

           if (preg_match($pattern, $string)) {
               echo "Valid SSN!";
           } else {
               echo "Invalid SSN.";
          }



-2) Write a regex pattern to match a 5-letter 
      word:

     Solution:

            /[\w]{5}/  


-3) Write a regex pattern to match a word 
      with one or more characters:

      Solution:

             /[\w]+/     




-4) Write a regex pattern to match the first 
      occurrence of 'my' in a subject string:

     Solution:

            /^my/  

     Explanation: This will match only the first 
           occurrence of the phrase 'my' within a 
           text (even if there are many 'my' 
           strings in the text).




-5) Write a regex pattern to match the last 'cats' in a subject string.

     Solution:

            /cats$/  

    Explanation: This will match only the last 
          occurrence of 'cats' in the subject text 
          (even if there are multiple 'cats' strings 
          in the text).



——————————————————————
	FUNCTIONS
——————————————————————

-A function is a piece of code that you write 
  to do something for you.
-If you want someone to do something for 
  you, depending on the task, you 
  sometimes need to hand them the 
  tools they need to do what needs to be 
  done. In programming, the tools you give to 
  a function should be passed in the 
  parentheses of the function, and they are 
  referred to as parameters or arguments.
-Function arguments are in the form of 
  literal values or variables, or even a 
  function-yes you can pass a function to a 
  function, in the case where part of the task 
  of that function is to call another function 
  that already does what the function you 
  are calling needs to do. 
-If the function takes (accepts) multiple 
  parameters, you just pass them to the 
  parentheses of the function separated by 
  commas.
-In programming, you have built-in functions 
  and custom functions. Custom functions 
  are functions you will write for your 
  application to do what it is meant to do. 
  However, there are a lot of functions built 
  into every programming language, created 
  by the authors of the language to help you 
  get other routine and common tasks done. 
  These are sometimes referred to as helper 
  functions. An example of such a helper 
  function in JavaScript is document.write() 
  which you can use to display some text in 
  the browser. You would agree that this is a 
  very common task that any programmer 
  will find themselves doing often. In fact, 
  every programming language has a way to 
  display some text or  the contents of a 
  data structure like a variable, an array, an 
  object etc. This is a common action 
  because knowing the contents of a data 
  object or being able to place text at certain 
  points in your code can be a quick and 
  handy way to debug errors in your code.
-The argument you pass to 
  document.write() in JavaScript is the text 
  you want to be displayed on screen at that 
  exact point in the code eg:


       var raining = true;

       if (raining == true)
       {
          document.write(“It is raining outside”);
       }





//———————————
CASSSS 
//----------------------
——————————————————————
	COOKIES
——————————————————————

-A cookie is a piece of code that your 
  program places on the computer of a user 
  to collect information about their 
  interaction with your program.
-It is often used to handle validation of 
  users, shopping cart management, the 
  customiSation of users’ experience with 
  your application etc.





——————————————————————
	AUTHENTICATION
——————————————————————

-This refers to the ways in which your computer program will try to identify and control who has access to your application.





——————————————————————
	SESSIONS
——————————————————————

-A session is the ability of your application to capture the start and end of a user’s interaction with your application.
-It is therefore being used to handle login and logout systems, after users are authenticated.





——————————————————————
	STRINGS
——————————————————————

-A string in computer programming refers to 
  the data structure that is a piece of text.
  A string is denoted by wrapping it within  a   
  pair of opening and closing quotation 
  marks.





——————————————————————
  SETTINGS AND CONFIGURATION
——————————————————————

          -Introduction
          -Dorguzen directory structure/skeleton
          -Dorguzen Configuration System
            -Files involved
            -Directories involved
            -How the System Works
            -Accessing Configuration Data
                -Via $GLOBALS
                -Via the config() Helper 
                -Via the DI Container
            -Dorguzen Environment Configuration (.env)
                -Security Recommendations
            -Summary
          -Runtime Database Settings (baseSettings)
            -What baseSettings Is
            -The Two-Tier Settings Design
            -What Belongs in File Config (Bootstrap Settings)
            -What Belongs in baseSettings (Runtime Settings)
            -Accessing baseSettings in Code
            -The baseSettings DB Table
            -Managing baseSettings via the Admin Panel
          -The Brand Slider
            -What It Is
            -How to Enable It
            -How to Configure the Image Source (brand_slider_source)
            -Adding Your Images
            -How It Works Internally
          -The SEO Module
            -What It Is
            -How It Works — the automatic pipeline
            -Step 1: Enable the module
            -Step 2: Enter global SEO data
            -Step 3: Enter per-page SEO data
                -The page name convention
            -What gets injected and where
                -Global SEO (site-wide meta tags)
                -Per-page head meta tags
                -Per-page body SEO data
                -Fallback title
            -Wiring up SEO in a layout file
            -Creating a custom layout
                -The layout skeleton
                -Parts of a layout
                -Multiple layouts / themes



        Introduction
        ------------
  This refers to the various ways in which the
configuration of your application can be 
handled in code at runtime. A good example is 
In the use of environmental variables, typically in an env 
File. It also involves all the knowledge around setting up 
your work station and tech stack for optimal performance.
Therefore, see Configuration to mean Settings.
It also involves information on the best practice for organising your project directory and file structure.

        


        Dorguzen Directory Structure/Skeleton
        ------------------------------

  Here is the Dorguzen directory structure:

  -assets
  -bootstrap
      -cache
        -config.php
        -config.php.meta
      -app.php 
      -config.php
      -helpers.php 
      -helpers_runtime.php
  -configs
      -app.php
      -modules
  -core 
      -Exceptions 
          -ValidationException.php
      -Psr 
          -PsrRequestAdapter.php 
          -SimpleRequestHandler.php
      -DGZ_Controllerp.php 
      -DGZ_Model.php 
      -etc 
  -css 
  -docs 
  -js 
  -lang
      -en 
      -fre
  -layouts
      -admin
      -dorguzApp
      -email
  -middleware
      -globalMiddleware  
        -BaseMiddleware.php
        -CsrfPsrMiddleware.php
        -FormValidationMiddleware/php
      -routeMiddleware
        -AuthMiddleware
  -modules
  -routes
      -api.php 
      -web.php
  -src 
      -api
        -DocsController.php
      -config
        -Config.php 
        -ConfigLoader.php 
        -EnvLoader.php
      -controllers
      -events
      -forms
      -jobs
      -listeners
      -forms
      -models 
      -services
      
  -storage (file uploads go here)
      -cache
        -routes.php
      -logs
  -tests
      -feature
      -manual
      -support
      -unit
  -vendor
  -views
      -home.php 
      -admin
  -composer.json
  -composer.lock
  -dgz
  -index.php
  -.env
  -.env.example
  -.env.local 
  -.env.local.example
  -.gitignore
  -.htaccess
  -phpunit.xml




        Dorguzen Configuration System
        -------------------------------
  The Dorguzen configuration system provides a centralized, cached, 
extensible, and environment-aware way to manage application settings across the entire framework.

It is designed around three core principles:

Single source of truth - configuration is loaded once and reused everywhere.

Zero re-instantiation - configuration is never reloaded or re-created.

Multiple access styles - global, helper-based, and DI-based access are all supported.

Files Involved
src/config/ConfigLoader.php

Responsible for discovering, loading, merging, and caching all configuration files.

Reads configuration files from:

configs/

configs/modules/

Supports multiple formats:

PHP

XML

YAML / YML

Handles config caching for performance

Returns a merged configuration array

This class does not expose configuration globally. It only loads it.

src/config/Config.php

The configuration repository.

This class:

Stores the final merged configuration array

Provides read/write access to config values

Acts as the single source of truth for config data

Common methods include:

all()

get(string $key, mixed $default = null)

has(string $key)

set(string $key, mixed $value)

getConfig() (backward compatibility)

getFileRootPath()

getHomePage()

…and other framework-specific helpers

Important:
Config does not load files. It only stores and retrieves configuration data.

src/config/EnvLoader.php

Responsible for loading .env files and exposing their values globally.

Reads .env key/value pairs

Injects them into $_ENV

Works seamlessly with the env() helper

You do not need to understand its internals to use it — only how to consume .env values.

Directories Involved
src/config/

Contains all core configuration-related classes:

Config

ConfigLoader

EnvLoader

configs/

The primary configuration directory for your application.

Example files:

configs/
├── app.php
├── database.php
├── mail.yaml
└── cache.xml

configs/modules/

Optional but recommended directory for module-specific configuration.

Example:

configs/modules/
├── blog.php
├── auth.yaml
└── payments.xml


All module configs are:

Automatically discovered

Automatically merged

Namespaced by filename

bootstrap/cache/ ⚠️ Required

This directory must exist and be writeable.

Used for:

Cached merged configuration

Cache metadata/signatures

bootstrap/cache/
├── config.php
└── config.php.meta


If this directory is missing or not writeable, configuration caching will fail.

How the System Works
Configuration Bootstrap (bootstrap/config.php)

The configuration system is bootstrapped early in the application lifecycle.

Key steps:

// Safety check
if (!defined('DGZ_BASE_PATH')) {
    throw new DGZ_Exception(
        'config.php must be loaded via index.php',
        DGZ_Exception::INVALID_CONFIG
    );
}

// Paths
$configDir = DGZ_BASE_PATH . '/configs';
$cacheFile = DGZ_BASE_PATH . '/bootstrap/cache/config.php';

// 1) Load .env (optional)
$envLoader = new EnvLoader(DGZ_BASE_PATH);
$envLoader->load('.env');

// 2) Load config files (with caching)
$loader = new ConfigLoader($configDir, $cacheFile, true);
$configArray = $loader->load();

// 3) Create config repository
$GLOBALS['config'] = new Config($configArray);

// 4) Register in DI container
$container = $GLOBALS['container'];
$container->singleton(Config::class, fn () => $GLOBALS['config']);

// 5) Return repository
return $GLOBALS['config'];

Accessing Configuration Data

Rule:
ONLY ONE INSTANCE of Config should ever exist.
Never instantiate it manually.

Dorguzen provides three safe and supported access methods.

1️⃣ Via $GLOBALS
$GLOBALS['config']->getConfig();
$GLOBALS['config']->get('app');
$appName = $GLOBALS['config']->get('app.name');
$debug = $GLOBALS['config']->get('app.debug', false);
$GLOBALS['config']->all();


✔ Direct
✔ Fast
✖ Verbose

2️⃣ Via the config() Helper (Recommended)

Defined in bootstrap/helpers.php.

config('app.name');
config('database.connections.mysql.host');
config('app.debug', false);


This is functionally equivalent to:

$GLOBALS['config']->get('app.name');


✔ Clean
✔ Readable
✔ Preferred in application code

3️⃣ Via the DI Container
use Dorguzen\Config\Config;

$config = container(Config::class);

$config->get('app.name');
$config->getConfig(); // backward compatibility


✔ Ideal for services, models, and controllers
✔ Test-friendly
✔ Framework-consistent

Why bootstrap/config.php Returns the Config Object

At the end of bootstrap/config.php:

$GLOBALS['config'] = new Config($configArray);
return $GLOBALS['config'];


This enables two valid usage styles.

Style 1: Global (Current)
require_once 'bootstrap/config.php';
config('app.name');

Style 2: Explicit (Future-Proof)
$config = require 'bootstrap/config.php';
$config->get('app.name');

Benefits

CLI compatibility

Testability

Optional DI usage

Zero downside

Matches patterns used by Laravel, Symfony, and Slim

👉  Think of bootstrap/config.php as a factory file, not just a script.





      Dorguzen Environment Configuration (.env)
      -------------------------------------------
  
  Dorguzen uses environment variables to store sensitive and environment-specific configuration 
such as database credentials, cache drivers, API keys, and runtime modes (local, staging, production).

This system is powered by:

  Dorguzen's EnvLoader

The battle-tested vlucas/phpdotenv package

The global env() helper

As a framework user, you do not need to understand the internals — only how to use .env files correctly.

1. Getting Started: .env.example

Dorguzen ships with two files named:

.env.example
.env.local.example


These files serve as a template showing all environment variables your application expects.

First step after installing Dorguzen
cp .env.example .env

and 

cp .env.local.example .env.local


You will now work only with .env and .env.local, while keeping .env.example and .env.local.example 
as references only.

✅ .env.example and .env.local.example are committed to Git
❌ .env and .env.local must never be committed

2. The .env Files

The .env files contain key-value pairs, one per line.

Supported syntax

Both quoted and unquoted strings, as well as booleans (true/false) are supported:

APP_NAME="ProjectName"
APP_ENV=local
APP_SLOGAN="The Best Service in Town"
CACHE_DRIVER=file


✔ Quotes are optional
✔ Spaces are allowed inside quoted strings
✔ Comments are allowed using #

3. Environment Detection (APP_ENV)

The variable APP_ENV tells Dorguzen which environment the application is running in.

Example:

APP_ENV=local


Common values include:

local

staging

production

This value is used by Dorguzen to determine which additional .env files to load.

4. Environment-Specific .env Files

Dorguzen supports multiple environment files, loaded in a predictable order.

Load order (important)

.env (base defaults)

.env.{APP_ENV} (environment-specific)

.env.local (local machine overrides)

Example

If your .env contains:

APP_ENV=local
CACHE_DRIVER=file


And your .env.local contains:

CACHE_DRIVER=redis


Then the effective value will be:

env('CACHE_DRIVER'); // redis

Common patterns
File	Purpose
.env	Base configuration
.env.local	Local developer overrides
.env.staging	Staging server config
.env.prod	Production server config

You can introduce as many environments as you like — Dorguzen will automatically load the matching file based on APP_ENV.

5. Git & Security Best Practices
These files must be ignored:
.env
.env.local
.env.staging
.env.prod

This file must be committed:
.env.example


This ensures:

Secrets never leak to GitHub

New developers know which variables are required

Production credentials remain server-only

In production, .env files are typically created directly on the server or injected via deployment tools, Docker, or CI/CD pipelines.

6. Using Environment Variables in Config Files

Environment variables are accessed using the global env() helper.

Example: configs/app.php
return [
    'appName' => env('APP_NAME'),
    'environment' => env('APP_ENV'),
    'cache_driver' => env('CACHE_DRIVER', 'file'),
];

Example: Database credentials
'localDBcredentials' => [
    'username'          => env('DB_LOCAL_USERNAME'),
    'pwd'               => env('DB_LOCAL_PASSWORD'),
    'db'                => env('DB_LOCAL_DATABASE'),
    'host'              => env('DB_LOCAL_HOST'),
    'connectionType'    => env('DB_LOCAL_CONNECTION'),
    'key'               => env('DB_LOCAL_KEY'),
],


The second argument to env() is an optional fallback value.

7. How env() Works (Conceptual)

The env() helper reads from PHP's environment:

getenv()

$_ENV

Fallback value (if provided)

Once loaded at bootstrap:

Environment variables are available globally

No container access is required

No performance cost at runtime

This makes environment variables ideal for:

Secrets

Credentials

Feature flags

Environment detection

8. Important Guidelines
✅ Do

Use env() only inside config files

Store secrets in .env, not in PHP code

Use .env.local for personal overrides

❌ Don't

Call env() deep inside application logic

Commit .env files

Hard-code credentials in configs

Once config files are loaded, your application should rely on config() — not env().

9. Summary

Dorguzen uses .env files for environment configuration

.env.example is your starting point

.env.local and .env.{APP_ENV} allow safe overrides

APP_ENV controls environment selection

env() bridges .env values into config files

Sensitive data never touches Git





    Security Recommendations
    --------------------------

✅ Never commit .env to Git

✅ Add .env to .gitignore

✅ Use .env.example for documentation

✅ Store secrets only in .env

✅ Load secrets into config via env()

❌ Do not hard-code credentials in config files

In production:

Use environment-level variables

Lock down file permissions

Ensure bootstrap/cache/ is writable but protected

Summary

✔ Centralized configuration
✔ Cached for performance
✔ Module-aware
✔ Environment-aware
✔ Single instance
✔ Multiple access styles




    Runtime Database Settings (baseSettings)
    ------------------------------------------

What baseSettings Is
---------------------
Dorguzen ships with a database table called `baseSettings`. Its purpose is to hold
a small number of settings that a site administrator needs to be able to change at
runtime through the admin panel — without touching any config files, redeploying, or
restarting the server.

These settings are loaded lazily by the Config class when first needed and are kept
entirely separate from the file-based configuration loaded at bootstrap. They do not
live in `configs/app.php`, they are not in `.env`, and they are not part of the
config cache. They are always read live from the database.


The Two-Tier Settings Design
-----------------------------
Dorguzen intentionally splits application settings into two tiers:

  Tier 1 — File-based config (bootstrap time)
    Loaded from `configs/` files and `.env` at the very start of every request.
    Cached for performance. Controlled by developers and deployment pipelines.
    Accessed via config('app.key') or $this->config->get('app.key').

  Tier 2 — Database settings (runtime, admin-controlled)
    Stored in the `baseSettings` DB table.
    Loaded lazily on first use during a request.
    Controlled by site administrators through the admin panel.
    Accessed via $this->config->getBaseSettings()['key'].

The division is a deliberate design decision: settings that are infrastructure or
code decisions belong in files, while settings that a non-developer site admin
might legitimately want to adjust belong in the database.


What Belongs in File Config (Bootstrap Settings)
-------------------------------------------------
Use `configs/app.php` and `.env` for settings that:

  - Are set once at deployment and rarely change
  - Require a server-level or code-level change to modify safely
  - Contain sensitive data (database credentials, API keys, JWT secrets)
  - Must be available before any DB connection is established
  - Are developer decisions, not admin decisions

Examples:
  - APP_NAME, APP_URL, APP_ENV
  - Database credentials (DB_HOST, DB_USERNAME, DB_PASSWORD)
  - JWT secret key
  - Mail/SMTP credentials
  - Whether the app is live or local
  - Default locale and fallback locale
  - Module on/off flags (seo, payments, sms)
  - allow_registration (a deployment decision, not a runtime toggle)


What Belongs in baseSettings (Runtime Settings)
------------------------------------------------
Use the `baseSettings` DB table for settings that:

  - A site admin should be able to change through the UI without code access
  - Affect the visual appearance or behaviour of the site, not its infrastructure
  - Do not contain sensitive data
  - Are safe for a non-developer to change
  - Take effect immediately on the next page load without any deployment step

Examples of settings that belong here:
  - show_brand_slider   — whether the brand image carousel is displayed site-wide
  - brand_slider_source — the path to the directory containing the slider images
  - app_color_theme     — the CSS colour theme applied to the site
  - Any future UI toggle an admin should control (e.g. maintenance mode banner,
    show/hide a promotions section, active site theme name)

A good rule of thumb: if changing the setting requires the admin panel, it belongs
in baseSettings. If changing it requires editing a file or running a deployment,
it belongs in file config.


Accessing baseSettings in Code
--------------------------------
The Config class provides a dedicated method for reading baseSettings. It is lazy-
loaded — the database is only queried the first time it is called on a given request.

    // In a controller, layout, or view partial:
    $baseSettings = $this->config->getBaseSettings();

    if ($baseSettings['show_brand_slider'] == 'true') {
        // render the brand slider
    }

    $colorTheme = $this->config->getAppColorTheme();
    // equivalent to $this->config->getBaseSettings()['app_color_theme']

Important: do NOT mix the two tiers. Do not call config('app.show_brand_slider') —
that key does not exist in the file config. And do not call getBaseSettings() to
read database credentials — they are not in the DB table. Each tier has its own
access method for a reason.


The baseSettings DB Table
--------------------------
The table has a simple structure:

    settings_id    INT  (primary key, auto-increment)
    settings_name  VARCHAR  (the key, e.g. 'show_brand_slider')
    settings_value VARCHAR  (the value, e.g. 'true')

Current entries:

    show_brand_slider    'true' or 'false'
                         Controls whether the brand image carousel is rendered
                         in supported layouts (seoMasterLayout, dorguzAppLayout).

    brand_slider_source  e.g. 'assets/images/gallery'
                         The path (relative to the app root) of the directory
                         from which the brand slider pulls its images.
                         See the Brand Slider section below for full details.

    app_color_theme      e.g. 'dark-blue'
                         The name of the CSS colour theme file to load.
                         Available themes are the CSS files in assets/css/color/.
                         The value here must match a filename in that directory
                         (without the .css extension).


Managing baseSettings via the Admin Panel
------------------------------------------
Log in as an admin and navigate to:

    Admin Dashboard → Settings

This page reads all rows from the `baseSettings` table and renders a form field
for each one. Submitting the form updates each setting's value in the database.
Changes take effect on the next page load — no restart or deployment required.

To add a new runtime setting:
  1. Insert a row directly into the `baseSettings` table:
         INSERT INTO baseSettings (settings_name, settings_value)
         VALUES ('my_new_setting', 'default_value');
  2. Add a corresponding form field for it in views/admin/manageSettings.php.
  3. Read it in your code via $this->config->getBaseSettings()['my_new_setting'].




    The Brand Slider
    -----------------

What It Is
-----------
The brand slider is a horizontal image carousel that sits between the main page
content and the footer on every page of your site. It is designed to showcase a
set of images — typically brand logos, product photos, or portfolio images — in
a continuously scrolling strip. It is powered by Owl Carousel.

It is a site-wide feature: when enabled, it appears on every page that uses a
supported layout. It is not a per-page decision. The two supported layouts are:

  - layouts/seoMaster/seoMasterLayout.php   (the recommended default layout)
  - layouts/dorguzApp/dorguzAppLayout.php   (the older full-featured layout)


How to Enable It
-----------------
The brand slider is toggled via the `show_brand_slider` setting in the
`baseSettings` database table. Set it to 'true' to show it, 'false' to hide it.

You can do this either:

  a) Through the admin panel (Settings page) — recommended for runtime changes.

  b) Directly in the database:
         UPDATE baseSettings SET settings_value = 'true'
         WHERE settings_name = 'show_brand_slider';

When set to 'false' the carousel HTML is not rendered at all — no empty space,
no placeholder, nothing. The layout renders as if the feature does not exist.


How to Configure the Image Source (brand_slider_source)
---------------------------------------------------------
The `brand_slider_source` setting tells the slider where to find its images. Its
value is a path relative to the application root — not a URL, not an absolute
server path. For example:

    assets/images/gallery

This means the slider will look for images inside:

    /your-app-root/assets/images/gallery/

To change it, update the DB value:

    UPDATE baseSettings SET settings_value = 'assets/images/myimages'
    WHERE settings_name = 'brand_slider_source';

Or change it through the admin panel Settings page.

The path must be relative to the application root (the directory containing
index.php). Do not include a leading or trailing slash. Do not use a URL — the
framework resolves the file system path and the browser URL separately from this
single relative value.


Adding Your Images
-------------------
Simply drop image files into the directory pointed to by `brand_slider_source`.
The slider automatically picks up every .jpg, .jpeg, .png, .gif, and .webp file
in that directory. There are no filenames to configure and no array to maintain.

Steps:
  1. Decide on your image directory (e.g. assets/images/gallery/).
  2. Update brand_slider_source in the DB to match (e.g. 'assets/images/gallery').
  3. Drop your images into that directory.
  4. Set show_brand_slider to 'true'.
  5. Load any page — the slider appears between the content and the footer.

To remove an image from the slider, delete the file from the directory.
To add more images, drop them into the directory. No code changes needed.

Dorguzen ships with a default gallery directory at assets/images/gallery/ seeded
with six demo images. These are placeholder images and should be replaced with
your own before going live.

For best visual results use images that are the same dimensions as each other.
Wide landscape images (e.g. 300×200px) work best for brand/logo sliders.


How It Works Internally
------------------------
In both seoMasterLayout and dorguzAppLayout, the brand slider block:

  1. Calls $this->config->getBaseSettings() to retrieve both show_brand_slider
     and brand_slider_source from the database.

  2. If show_brand_slider is not 'true', nothing is rendered.

  3. Builds the absolute file system path from DGZ_BASE_PATH (the app root
     constant defined in index.php) and the brand_slider_source value, then
     uses PHP's glob() to discover all image files in that directory.

  4. Renders an <img> tag for each file found, building the browser URL from
     getFileRootPath() (which resolves correctly for both local and live
     environments) and the relative source path.

  5. The #brands-carousel div is initialised by Owl Carousel, which is already
     loaded by both layouts' html_dependencies files.

The brand slider shares the same Owl Carousel library used by other carousels
in the layouts — no additional JavaScript or CSS dependencies are needed.




    The SEO Module
    ---------------

    Contents
    ~~~~~~~~~
        - What It Is
        - How It Works — the automatic pipeline
        - Step 1: Enable the module
        - Step 2: Enter global SEO data
        - Step 3: Enter per-page SEO data
            - The page name convention
        - What gets injected and where
            - Global SEO (site-wide meta tags)
            - Per-page head meta tags
            - Per-page body SEO data
            - Fallback title
        - Wiring up SEO in a layout file
        - Creating a custom layout
            - The layout skeleton
            - Parts of a layout
            - Multiple layouts / themes



    What It Is
    ~~~~~~~~~~~
    The SEO module is a built-in Dorguzen module (modules/seo/) that gives you a
    database-driven way to manage the SEO meta tags of every page on your site,
    as well as site-wide Open Graph and social metadata, without touching a single
    view file or layout after the initial setup.

    It ships with two data stores:

        seo              Per-page SEO records. Each row targets one view by name
                         and holds the page title, meta description, keywords,
                         Open Graph tags, Twitter Card tags, canonical href,
                         noindex flag, and body content fields (h1, h2, page copy)
                         — all in up to three languages (en, fr, es).

        seo_global       One row per application. Holds the site-wide meta tags
                         that should appear on every page: OG locale, OG site name,
                         article publisher/author, geo coordinates, Facebook app ID,
                         Twitter card type/handle, and hreflang alternate links.

    Both are managed through the admin UI at /seo (visible only to admin roles).



    How It Works — the automatic pipeline
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    You do not need to call anything from your controllers. When a view is rendered,
    DGZ_Controller::display() calls:

        $this->loadSeoData($this->viewName);
        $layout->setGlobalSeoData($this->globalSeoData);
        $layout->setMetadata($this->getMetadata());

    loadSeoData() does the following automatically:
        1. Checks that the seo module is enabled in config ('seo' => 'on').
        2. Instantiates SeoController and fetches the global SEO row.
        3. Builds an array of HTML <meta> tag strings from the global row and
           stores them on the controller via setGlobalSeoData().
        4. Looks up a per-page SEO row whose seo_page_name matches the current
           view's class name (lowercased). If found, builds head and body SEO
           data arrays and stores them.
        5. Passes both arrays to the layout via setGlobalSeoData() and
           setMetadata() — making them available to getGlobalSeoData() and
           getMetadata() inside the layout's display() method.

    If the seo module is off, or no matching row is found, nothing is output and
    the layout falls back to a plain <title> tag (see Fallback title below).

    The only requirement on your part is two lines in the layout file's <head>
    section (see "Wiring up SEO in a layout file" below).



    Step 1: Enable the module
    ~~~~~~~~~~~~~~~~~~~~~~~~~~
    In configs/app.php (and optionally .env), ensure the seo flag is 'on':

        // configs/app.php
        'modules' => [
            'seo' => env('MODULES_SEO_STATUS', 'on'),
            ...
        ],

        # .env
        MODULES_SEO_STATUS=on

    The module is on by default in a fresh Dorguzen installation.



    Step 2: Enter global SEO data
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Log in as an admin user and navigate to:

        /seo  →  SEO Manager  →  "Global SEO" tab (or the "Add Global SEO" link)

    Fill in the site-wide fields:

        OG Locale              e.g. en_GB  — the primary language/region of the site
        OG Site Name           Your website's display name shown in social cards
        Article Publisher      Full URL to your Facebook business page
        Article Author         Full URL to your Facebook personal page
        Geo Placename          e.g. London  — the city/region the site represents
        Geo Region             e.g. GB  — ISO country/region code
        Geo Position           e.g. 51.5074;-0.1278  — lat;lon coordinates
        Facebook App ID        Your FB app ID for the fb:app_id meta tag
        Twitter Card Type      e.g. summary, summary_large_image
        Twitter Site Handle    Your Twitter/X handle e.g. @mysite
        HREFlang Alternate 1   e.g. fr-ca  — alternate language variant URL
        HREFlang Alternate 2   e.g. en-ca

    You only need to fill in the fields that apply to your site. Any field left
    blank is silently skipped — no empty <meta> tags are output.

    These tags will appear in the <head> of every page on your site automatically,
    in the same request cycle as the page render — no cache to clear.



    Step 3: Enter per-page SEO data
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Navigate to:

        /seo  →  "Add Page SEO" link

    Fill in the fields for the page you want to optimise:

        Page Name           The view name (see convention below)
        Title (en/fr/es)    Meta title — max 60 characters
        Description (en)    Meta description — max 150 characters
        Keywords            Comma-separated keywords
        OG Title            Open Graph title for social sharing
        OG Description      Open Graph description
        OG Image            Fully-qualified image URL e.g. https://mysite.com/assets/social/og.png
        OG Image Secure URL HTTPS version of the OG image URL
        OG Image Width/Height  Dimensions in pixels
        OG Video            HTTPS URL of a video for rich cards
        OG Type             e.g. website, article, product
        OG URL              Canonical URL of the page for OG
        Twitter Title       Title shown in Twitter/X cards
        Twitter Description Description shown in Twitter/X cards
        Twitter Image       Image URL for Twitter cards
        Canonical Href      Canonical link rel href (tick to enable)
        No Index            Tick to add <meta name="robots" content="noindex">
        SEO Dynamic         Tick if this page's title/description come from
                            user-submitted content — see Dynamic SEO below
        H1 Text             The recommended h1 heading for this page
        H2 Text             The recommended h2 sub-heading
        Page Content        Keyword-rich body copy for this page


    The page name convention
    ~~~~~~~~~~~~~~~~~~~~~~~~~
    The page name you enter MUST exactly match the lowercased class name of the
    view file for that page. Dorguzen matches by calling strtolower($viewName)
    and looking up seo_page_name in the database.

    Examples:

        View file                              Page name to enter
        -----------------------------------    ------------------
        views/home.php  (class home)           home
        views/about.php (class about)          about
        views/contact.php (class contact)      contact
        modules/blog/views/post.php            post

    If the class is named differently from the file, use the class name, not
    the filename. The match is always against the class name, lowercased.



    What gets injected and where
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    Global SEO (site-wide meta tags)
    ---------------------------------
    Injected into the layout's <head> via getGlobalSeoData(). These appear on
    every page and cover OG site identity, geo tags, Twitter card base settings,
    and hreflang alternates.

    The tags output by getGlobalSeoData() include (where data exists):

        <meta property="og:locale:alternate" content="..." />
        <meta property="og:site_name" content="..." />
        <meta property="article:publisher" content="..." />
        <meta property="article:author" content="..." />
        <meta name="geo.placename" content="..." />
        <meta name="geo.region" content="..." />
        <meta name="geo.position" content="..." />
        <meta property="fb:app_id" content="..." />
        <meta name="twitter:card" content="..." />
        <meta name="twitter:site" content="..." />
        <link rel="alternate" href="..." hreflang="..." />


    Per-page head meta tags
    ------------------------
    Injected via getMetadata(). These are page-specific and override the fallback
    <title> tag. For the current page's language, the following are output:

        <meta name="description" content="...">
        <meta name="keywords" content="...">
        <meta property="og:title" content="..." />
        <meta property="og:description" content="..." />
        <meta property="og:image" content="..." />
        <meta property="og:image:secure_url" content="..." />
        <meta property="og:image:width" content="..." />
        <meta property="og:image:height" content="..." />
        <meta property="og:video" content="..." />
        <meta property="og:type" content="..." />
        <meta property="og:url" content="..." />
        <meta name="twitter:title" content="..." />
        <meta name="twitter:description" content="..." />
        <meta name="twitter:image" content="..." />
        <link rel="canonical" href="..." />
        <meta name="robots" content="noindex">
        <title>...</title>


    Per-page body SEO data
    -----------------------
    Three fields from the per-page SEO row are also made available for use
    inside the view's body content: h1 text, h2 text, and page copy. These are
    stored on the layout object and can be accessed in views or layout partials
    via the controller's getBodySeoData() method:

        $bodySeo = $this->controller->getBodySeoData();
        // keys: 'seo_h1_text', 'seo_h2_text', 'seo_page_content'

    These fields are optional. If they are empty in the database row, they
    return empty strings. Typical usage is to output them as the primary
    heading and introductory copy on a page where you want to manage that
    content from the admin panel rather than hard-coding it in the view.

    Example in a view:

        <?php $bodySeo = $this->controller->getBodySeoData(); ?>
        <h1><?= htmlspecialchars($bodySeo['seo_h1_text']) ?></h1>
        <h2><?= htmlspecialchars($bodySeo['seo_h2_text']) ?></h2>
        <p><?= htmlspecialchars($bodySeo['seo_page_content']) ?></p>


    Fallback title
    ---------------
    If no per-page SEO row exists for the current view (or the SEO module is
    off), getMetadata() returns an empty string. The layout handles this with
    the following pattern (already in seoMasterLayout.php line 22):

        <?=($this->getMetadata() != null) ? $this->getMetadata() : "<title>".self::$appName."-".$this->pageTitle."</title>" ?>

    This means every page always has a <title> tag — either the SEO-managed one
    or the auto-generated "appName - pageTitle" fallback. No extra work needed.



    Wiring up SEO in a layout file
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    If you create a custom layout (see next section), you must include exactly
    two lines inside the <head> tag to connect it to the SEO pipeline:

        <!-- inside <head> -->
        <?= $this->getGlobalSeoData() ?? '' ?>
        <?= ($this->getMetadata() != null) ? $this->getMetadata() : "<title>" . self::$appName . "-" . $this->pageTitle . "</title>" ?>

    Line 1 outputs the site-wide global meta tags (OG identity, geo, Twitter base).
    Line 2 outputs the page-specific meta tags including <title>, or falls back to
    an auto-generated title if no SEO row exists for this page.

    Place them immediately after the required <meta charset> and <meta viewport>
    tags, before any <link> or <script> tags, to ensure search engines read them
    in the correct order. That is all the setup required — the framework handles
    the rest automatically on every request.



    Creating a custom layout
    ~~~~~~~~~~~~~~~~~~~~~~~~~
    A layout in Dorguzen is the outer HTML shell that wraps every page — the
    <html>, <head>, header, footer, and the slot where view content is injected.
    You can have as many layouts as your application needs: a public site layout,
    an admin panel layout, a minimal API response layout, a campaign landing page
    layout, and so on. Dorguzen ships with:

        layouts/seoMaster/       The recommended public-facing layout (default)
        layouts/admin/           The admin panel layout
        layouts/dorguzApp/       An alternative full-featured public layout

    To create a new layout, copy the seoMaster directory and rename it:

        layouts/
        └── myTheme/
            ├── myThemeLayout.php          ← the main layout class (required)
            ├── header.inc.php             ← navigation / top bar
            ├── footer.inc.php             ← footer links / copyright
            ├── html_dependencies_top.inc.php    ← CSS links, early scripts
            └── html_dependencies_bottom.inc.php ← JS bundles loaded at end of body


    The layout skeleton
    ~~~~~~~~~~~~~~~~~~~~
    Your main layout class must extend DGZ_Layout and implement display(). Use
    seoMasterLayout.php as your starting point. The minimal required structure:

        <?php
        namespace Dorguzen\layouts\myTheme;

        class myThemeLayout extends \Dorguzen\Core\DGZ_Layout
        {
            public function display()
            { ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">

                    <!-- SEO — these two lines are required for the SEO module to work -->
                    <?= $this->getGlobalSeoData() ?? '' ?>
                    <?= ($this->getMetadata() != null) ? $this->getMetadata() : "<title>" . self::$appName . "-" . $this->pageTitle . "</title>" ?>

                    <?= $this->getCssHtml() ?>
                    <?php include('html_dependencies_top.inc.php'); ?>
                </head>
                <body>

                    <?php include('header.inc.php'); ?>

                    <!-- Flash messages -->
                    <?php if (!empty($this->errors)): ?>
                        <div class="alert danger"><?= $this->errors ?></div>
                    <?php endif; ?>
                    <?php if (!empty($this->successes)): ?>
                        <div class="alert success"><?= $this->successes ?></div>
                    <?php endif; ?>

                    <!-- Page content injected here -->
                    <?= $this->content ?>

                    <?php include('footer.inc.php'); ?>

                    <?php include('html_dependencies_bottom.inc.php'); ?>
                    <?= $this->getJavascriptHtml() ?>

                </body>
                </html>
            <?php
            }
        }

    Important lines explained:

        $this->getCssHtml()           Outputs any per-view <link> tags added via
                                      $this->addStyle('file.css') in a view's show()
        $this->getJavascriptHtml()    Outputs any per-view <script> tags added via
                                      $this->addScript('file.js')
        $this->content                The rendered HTML of the current view
        $this->errors / successes     Flash message bags — include all five:
                                      exceptions, warnings, errors, notices, successes
        include('header.inc.php')     Pull in the navigation partial
        include('footer.inc.php')     Pull in the footer partial


    Parts of a layout
    ~~~~~~~~~~~~~~~~~~
    Splitting the layout into partials (header, footer, dependencies) keeps each
    file focused and easy to edit. The split is a convention — you can merge them
    or add more partials as your design grows. Common patterns:

        header.inc.php
            Site logo, main navigation, mobile menu toggle, any top banner.
            Read the current route with $this->config->getCurrentRoute() to
            highlight the active nav item.

        footer.inc.php
            Footer links, social icons, copyright notice, cookie consent popup.

        html_dependencies_top.inc.php
            CSS framework links (Bootstrap, Tailwind etc.), icon libraries,
            Google Fonts, and any scripts that must load in <head>.

        html_dependencies_bottom.inc.php
            JavaScript bundles (jQuery, Bootstrap JS, Owl Carousel, app.js).
            Placing these at the bottom improves perceived page load speed.


    Multiple layouts / themes
    ~~~~~~~~~~~~~~~~~~~~~~~~~~
    To use a specific layout for a controller action, call setLayout() before
    rendering:

        // In a controller method
        $this->setLayout('myTheme', 'myThemeLayout');
        $this->renderView('myView');

    To make a layout the default for the whole application, set it in
    configs/app.php:

        'layoutDirectory' => 'myTheme',
        'defaultLayout'   => 'myThemeLayout',

    The admin panel uses its own layout (layouts/admin/adminLayout.php)
    automatically for all views in views/admin/ that extend DGZ_AdminHtmlView.
    You do not need to call setLayout() for admin views — the base class
    handles it.

    Each layout is fully independent. A marketing landing page layout can be
    stripped down to a single column with no navigation. A dashboard layout can
    include a sidebar and data widgets. A campaign page layout can load a
    completely different CSS framework. None of these choices affect any other
    layout.




——————————————————————
	SECURITY
——————————————————————

-This is about a list of ways in which your 
  computer can be secured against 
  malicious users and attackers

-This will involve best practices on all areas 
  of computer programming, from tips on 
  handling user input authentication, 
  database access, form input processing, 
  API security, securing network 
  communications and more.
-This will be the favoUrite area of a 
  cybersecurity enthusiast or professional.

-Securing your application requires taking a 
  close look at all areas where users have 
  access to the application. These are areas 
  where they come in contact with your 
  application and can therefore 
  communicate with it. These are the key 
  areas where security measures should be 
  concentrated. As a guide, here is a list of 
  these crucial points of contact:

     -Web forms and SQL insertions
     -SSL implementation 
     -APIs. JSON Web Tokens (JWTs) can 
           help here.
     -URL parameters
     -open ports of your server (unseen).
           Network firewalls can help here.
     -Cookies management. Being aware of 
           its limitations.
     -User session management. Finding 
           ways to prevent user session fixation.
           Multi-factor authentication (MFA) can 
           help here.



                         -User Roles and View-Level Authentication
                            -The four user roles
                            -How roles are stored (session keys)
                            -The Auth() helper — full method reference
                            -The views/admin/ directory and DGZ_AdminHtmlView
                                -How the guard works
                                -The setContext() hook
                                -Which views belong in views/admin/
                            -Role-based access — tips and patterns
                                -Restricting controller actions by role
                                -Showing/hiding UI elements by role
                                -The middleware alternative (route groups)
                                -Feature-level permissions — Auth()->can()

                         -Using the DGZ_Validator
                         -Examples
                            —Using the validator in controllers
                            -Validating JSON payload for API
                            -A Custom callback rule (closure or callable)
                        -Rules available (built in)
                        -Messages & translations
                        -Performance and extension notes
                        -Full controller example flow
                        -Short tutorial
                        -DGZ Validation Rules - full list with usage and Custom messages
                        -Form submission and controller example
                            -Controller validation





    User Roles and View-Level Authentication
    -----------------------------------------

    Dorguzen ships with a four-tier role system built into the users table. Every
    registered user has a `users_type` column whose value is one of:

        Role          Description
        ----------    -------------------------------------------------------
        member        A regular registered user of the application. The default
                      role assigned at registration. Can access their own
                      account pages but has no admin capabilities.

        admin         A regional/location manager. Typically responsible for
                      moderating content (ads, orders, etc.) within one or more
                      assigned geographic locations. Cannot manage other admins.

        admin_gen     A general administrator. Has full access to all regions
                      and can manage admin-level users. Cannot manage
                      super_admin accounts.

        super_admin   Full unrestricted access. The only role that can manage
                      general admins, delete users of any type, and perform
                      irreversible platform-wide operations.




    How roles are stored
    ~~~~~~~~~~~~~~~~~~~~~
    When a user logs in via `Auth()->login()` or `AuthController::doLogin()`,
    the following session keys are written:

        $_SESSION['authenticated']   'Let Go-{appName}'  — presence proves login
        $_SESSION['start']           Unix timestamp of login time
        $_SESSION['custo_id']        the user's numeric primary key
        $_SESSION['user_type']       one of the four role strings above
        $_SESSION['username']        the user's username
        $_SESSION['email']           the user's email address
        $_SESSION['first_name']
        $_SESSION['last_name']
        $_SESSION['google_id']       populated if user registered via Google OAuth
        $_SESSION['phone_number']
        $_SESSION['mm_account']      mobile money account number, if set
        $_SESSION['emailverified']   'yes' | 'no'
        $_SESSION['created']         account creation timestamp

    Admin logins (any role other than member) are additionally written to the
    logs table automatically.

    Note: $_SESSION['user_type'] is written at login and stays unchanged for the
    duration of the session. DGZ_Auth does NOT rely on it for role checks —
    isAdmin(), hasRole(), can(), and role() all read from the Users model that is
    loaded fresh from the database on every request (see above). This means if a
    user's type is changed in the DB, Auth() reflects it immediately on their next
    request without requiring a re-login. $_SESSION['user_type'] is used only by
    DGZ_AdminHtmlView::guardAdminAccess() and is available as a convenience for
    raw session reads in edge cases.




    The Auth() helper — full method reference
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    `Auth()` returns the singleton `DGZ_Auth` instance. It is available globally
    in controllers and views via the `Auth()` helper function (bootstrap/helpers.php).

    IMPORTANT — Auth() reads from the Users model, not from $_SESSION['user_type'].
    At construction, DGZ_Auth calls loadUserFromSession(), which loads a fresh Users
    model from the database using $_SESSION['custo_id']. All role and identity methods
    (isAdmin, hasRole, can, role, username, id) read from that model object. This means
    Auth() always reflects the current database state, not the session snapshot set at
    login time. DGZ_AdminHtmlView::guardAdminAccess() is the only place that reads
    $_SESSION['user_type'] directly — it does so intentionally, before DGZ_Auth is
    available in the view lifecycle.


        Method                        Returns    Description
        ----------------------------  ---------  -------------------------------------------
        Auth()->check()               bool       true if a user is logged in (model loaded
                                                 successfully from DB)
        Auth()->guest()               bool       true if NO user is logged in
        Auth()->id()                  ?int       the current user's primary key, or null
        Auth()->user()                ?object    the full Users model object for the current
                                                 user, or null if not logged in
        Auth()->username()            ?string    the current user's username, or null
        Auth()->userType()            ?string    the current user's tier string
                                                 (e.g. 'admin_gen'), or null if not logged in
        Auth()->isAdmin()             bool       true if tier is admin, admin_gen, or
                                                 super_admin; false if not logged in
        Auth()->isType($type)         bool       true if the user's tier exactly matches
                                                 $type; false if not logged in
        Auth()->can($feature)         bool       true if the current user's tier is in the
                                                 allowed list for $feature in the
                                                 configs/Config.php 'permissions' map;
                                                 false if not logged in or feature not mapped
        Auth()->permissions()         array      all feature aliases accessible to the
                                                 current user's tier; empty array if not
                                                 logged in
        Auth()->hasRoles()            array      convenience alias for permissions()
        Auth()->isEmailVerified()     bool       true if users_emailverified == 'yes'
        Auth()->login($u,$p,          bool       attempt login; $rememberMe sets a 4-day
                      $rememberMe)               cookie; returns true on success
        Auth()->logout()              void       destroy session, clear cookies, redirect
                                                 to auth/login

    Examples:

        // In a controller — bounce non-logged-in visitors
        if (Auth()->guest()) {
            $this->redirect('auth', 'login');
        }

        // Only let admins proceed
        if (!Auth()->isAdmin()) {
            $this->redirect('home');
        }

        // Check an exact user tier
        if (Auth()->isType('super_admin')) {
            // only super_admin reaches here
        }

        // Check feature-level permission (config-driven)
        if (!Auth()->can('seo')) {
            $this->redirect('home');
            return;
        }

        // Get all features the current user can access
        $features = Auth()->permissions();  // e.g. ['seo', 'manage_users']
        $features = Auth()->hasRoles();     // same result — convenience alias

        // In a view — show a link only to admins
        <?php if (Auth()->isAdmin()): ?>
            <a href="<?= $this->controller->config->getFileRootPath() ?>user/dashboard">
                Admin Panel
            </a>
        <?php endif; ?>




    The views/admin/ directory and DGZ_AdminHtmlView
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    All view files that render admin backend pages live in views/admin/ and extend
    `\Dorguzen\Core\DGZ_AdminHtmlView` instead of the usual `\Dorguzen\Core\DGZ_HtmlView`.

    The distinction matters:

        views/                       Accessible to any visitor (logged in or not)
        └── home.php                 Public pages extend DGZ_HtmlView
        └── details.php
        └── ...

        views/admin/                 Admin-only pages — extend DGZ_AdminHtmlView
        └── allAds.php
        └── manageUsers.php
        └── goldUsers.php
        └── ...




    How the guard works
    ~~~~~~~~~~~~~~~~~~~~
    `DGZ_AdminHtmlView` overrides `setContext()`, the method the framework calls to
    inject the current controller into every view before `show()` is called. This
    makes it the earliest safe point at which config (and therefore the app name)
    is available — without it, the session token comparison cannot be made.

        class DGZ_AdminHtmlView extends DGZ_HtmlView
        {
            private const ADMIN_TYPES = ['admin', 'admin_gen', 'super_admin'];

            public function setContext(DGZ_Controller &$pageController): void
            {
                parent::setContext($pageController);   // store the controller
                $this->guardAdminAccess();             // immediately run the guard
            }

            private function guardAdminAccess(): void
            {
                $expectedToken   = 'Let Go-' . $this->controller->config->getConfig()['appName'];
                $isAuthenticated = isset($_SESSION['authenticated'])
                                   && $_SESSION['authenticated'] === $expectedToken;
                $isAdmin         = isset($_SESSION['user_type'])
                                   && in_array($_SESSION['user_type'], self::ADMIN_TYPES, strict: true);

                if ($isAuthenticated && $isAdmin) { return; }

                // Not authorised — redirect to login and halt all further execution.
                header('Location: ' . $this->controller->config->getFileRootPath() . 'auth/login');
                exit;
            }
        }

    The guard checks two things:
        1. $_SESSION['authenticated'] equals 'Let Go-{appName}' — proves the user
           completed a real login, not just set the session key manually.
        2. $_SESSION['user_type'] is admin, admin_gen, or super_admin — proves the
           logged-in user actually has admin rights. A regular member who is
           authenticated will still be redirected.

    If either check fails the visitor is sent to auth/login immediately. No HTML
    from the view's show() method is rendered at all.





    Which views belong in views/admin/
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Place a view in views/admin/ (and have it extend DGZ_AdminHtmlView) when:
        - The page is part of the backend CMS / admin dashboard
        - It displays sensitive data (all users, all orders, transactions, logs)
        - It lets the user perform privileged actions (delete ads, manage users,
          change settings, approve gold memberships, etc.)

    Keep a view in views/ (extending DGZ_HtmlView) when:
        - The page is part of the public-facing front end
        - It is accessible to guests or regular members
        - Any finer-grained access control inside that view is done inline using
          Auth() checks or $_SESSION['user_type'] comparisons

    NOTE: having views/admin/ as a separate directory is itself a readability and
    maintainability benefit — it visually separates the admin CMS from the rest of
    the application, making it immediately clear to any developer which files
    power the backend.




    Role-based access — tips and patterns
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    1. Restricting controller actions by role

       The cleanest place to enforce role checks is at the top of a controller
       method, before any data is fetched. Always return (or exit) after calling
       redirect() so no further code in the method runs:

       a) Feature-level check (recommended for module/feature access):

           public function index(): void
           {
               if (!Auth()->can('seo')) {
                   $this->redirect('home');
                   return;
               }
               // safe to proceed — current user's type is in the 'seo' allowed list
           }

       b) Admin-or-above check (any of the three admin roles):

           public function dashboard(): void
           {
               if (!Auth()->isAdmin()) {
                   $this->redirect('auth', 'login');
                   return;
               }
               // ...
           }

       c) Exact tier check (one specific tier only):

           public function deleteUser(int $userId): void
           {
               if (!Auth()->isType('super_admin')) {
                   $this->redirect('home');
                   return;
               }
               // only super_admin reaches here
           }

       d) Multiple tiers without a feature map entry:

           public function manageRegion(): void
           {
               $type = Auth()->userType();
               if ($type !== 'admin_gen' && $type !== 'super_admin') {
                   $this->redirect('home');
                   return;
               }
               // ...
           }

       Rule of thumb: use can() when the access rule is feature-based and may apply
       to several tiers; use isType() when the rule is strictly tied to one exact
       tier; use isAdmin() when any admin tier is sufficient.

    2. Showing / hiding UI elements by role

       In a view, use Auth() to conditionally render controls:

           <?php if (Auth()->isType('super_admin')): ?>
               <a href="..." class="btn btn-danger">Delete User</a>
           <?php elseif (Auth()->isAdmin()): ?>
               <a href="..." class="btn btn-primary" disabled>Delete User</a>
               <small>Super admin only</small>
           <?php endif; ?>

           <?php if (Auth()->can('seo')): ?>
               <a href="<?= $root ?>seo">SEO Manager</a>
           <?php endif; ?>

       Prefer Auth() over reading $_SESSION['user_type'] directly. The session value
       is a snapshot set at login time; Auth() reads from the Users model loaded fresh
       from the database on every request, so it always reflects the current state.

    3. The middleware alternative (route groups)

       The DGZ_AdminHtmlView guard described above works well for auto-discovery
       routes (i.e. URLs that the framework resolves automatically without an
       explicit route definition). For defined routes in routes/web.php, the
       architecturally preferred approach is a route middleware group:

           // routes/web.php
           $router->middleware(['auth', 'admin'])->group(function () use ($router) {
               $router->get('/admin/users',       'AdminController@manageUsers');
               $router->get('/admin/deleteAd',    'AdminController@deleteAd');
               $router->get('/admin/goldUsers',   'UserController@goldUsers');
               // ... all admin routes
           });

       With this approach the middleware runs before the controller is even
       instantiated, and the admin views can safely extend the plain DGZ_HtmlView
       because the middleware has already guaranteed that only admins reach them.

       The two approaches are not mutually exclusive — you can use middleware on
       defined routes AND DGZ_AdminHtmlView on auto-discovery admin views. Having
       both ensures that no admin view is ever accidentally reachable without a
       valid admin session, regardless of how the URL is resolved.


    4. Feature-level permissions — Auth()->can()

       For features that cut across user types (e.g. only certain roles may access
       the SEO module, regardless of whether the page is "admin" or not), Dorguzen
       provides a config-driven permissions map.

       The map lives in configs/Config.php under the 'permissions' key:

           'permissions' => [
               'seo'          => ['admin', 'admin_gen', 'super_admin'],
               'payments'     => ['admin_gen', 'super_admin'],
               'manage_users' => ['admin', 'admin_gen', 'super_admin'],
               'settings'     => ['super_admin'],
           ],

       Each key is a feature alias. The value is the list of user types allowed
       to access it. To add a new feature, add a key/array pair here.

       Checking access anywhere in the application:

           Auth()->can('seo')         // true  if the logged-in user's type
                                      //       is in the 'seo' allowed list
           Auth()->can('settings')    // true  only for super_admin

       If the user is not logged in, can() always returns false.
       If the feature key does not exist in the map, can() returns false.

       Guarding a controller action:

           public function index(): void
           {
               if (!Auth()->can('seo')) {
                   $this->redirect('home');
                   return;
               }
               // ... render the page
           }

       Guarding inside a view:

           <?php if (Auth()->can('seo')): ?>
               <!-- SEO module content -->
           <?php else: ?>
               <h3>You do not have permission to access this page.</h3>
           <?php endif; ?>

       Method distinctions:
           isType($type)    — raw tier check: is this user's tier exactly $type?
           can($feature)    — feature check: is this user's tier in the allowed
                              list for $feature?
           permissions()    — returns all feature aliases this user's tier can access
           hasRoles()       — convenience alias for permissions()




    Using the DGZ_Validator
    --------------------------
    Dorguzen provides you with a robust validation object to secure all kinds of requests 
  to your web application. This includes forms anD API requests. First of all, be aware that 
  the validator is available to all your controllers as long as they extend the parent controller 
  DGZ_Controller as they should. They will then be able to access the validator via their validator 
  property, or an accessor convenient method validator() like so: 

     $this->validator ... 

  or 

     $this->validator(...)

  Let's break down this DGZ_Validator to you completely master how it works.



    Examples — Using the validator in controllers
    ---------------------------------------------

  Example A — validating a web form submission (classic)

    public function saveProfile()
    {
        $input = $_POST; // or use sanitized input wrapper you already have

        $rules = [
            'first_name' => 'required|string|min:2|max:50',
            'last_name'  => 'required|string|min:2|max:50',
            'email'      => 'required|email',
            'age'        => 'nullable|integer|between:18,120',
            'bio'        => 'nullable|string|max:1000',
        ];

        $validator = $this->validator($input, $rules, [
            'email.required' => 'We need your email address.',
            'age.between' => 'You must be at least 18.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            // send back to view: 
            $this->addErrors($errors, 'Validation failed');

            // redirect back to another controller & method
            $this->redirect('auth', 'dashboard');

            or render a view and pass in the error data
            $view = Dorguzen\Core\DGZ_View::getView('register', $this, 'html');
            $view->show(['errors' => $errors, 'old' => $input]);
        }

        // validation passed — proceed to save
        ...
    }

  

  Validating JSON payload for API
  --------------------------------

    public function apiCreateAd()
    {
        $payload = json_decode(file_get_contents('php://input'), true) ?: [];

        $rules = [
            'title' => 'required|string|min:3|max:120',
            'price' => 'required|numeric|min:0',
            'category_id' => ['required','integer'],
            'images' => 'nullable|array',
        ];

        $validator = $this->validator($payload, $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            header('Content-Type: application/json', true, 422);
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }

        // continue...
    }



  A Custom callback rule (closure or callable)
  ---------------------------------------------

A rule can be a closure or a callback:myFuncName:

    $rules = [
        'username' => [
            'required',
            'min:5',
            function($value, $all, $field) {
                if (!preg_match('/^[a-z0-9_-]+$/i', $value)) {
                    return 'Only letters, numbers, - and _ allowed.';
                }
                // return true if passes
                return true;
            }
        ],
        'unique_slug' => 'required|callback:\Client\Helpers::isUniqueSlug'
    ];

In this closure above, the 3 parameter values $value, $all, and $field come from the validator engine. 
For example: 

  $value is the value of the single field currently being validated eg 'johnny_walker'.
  $all is the full data array being validated (all request data-the entire submitted form).
  $field is the field name currently being validated eg 'username'.

For example, here is another function (closure) you can use to ensure that the user's email address is not 
the same as their username:

  function($value, $all, $field) {
    // Example: ensure email does not equal username
    if ($field === 'email' && $value === $all['username']) {
        return 'Email cannot be the same as username';
    }

    return true;
}

If the callable returns true validation passes; if it returns false or a string, failure message is 
registered (string used as error message if provided).

When you write:

    'unique_slug' => 'required|callback:\Client\Helpers::isUniqueSlug'

this means:

  -there exists a class Helpers inside namespace \Client
  -inside that class there is a static method: isUniqueSlug($value)
  -and that method must return true or false

Typically, you would want you callback method/function to be one that is in the same controller where 
the validation is being done. Say for example, in the current controller you have a method named 
validateCustomerAccount(), here is an example syntax of how you will use that method in the callback of 
the validation rules:

      $rules = [
          'fieldName' => 'required|callback:$this->validateCustomerAccount'
      ];

Note that any arguments that the callback function needs-which is data submitted from the form will be 
automatically injected for you by the validator from the data array you passed in to it.

  You could just as well make this callback be any method in your application. For example, if you wish
to call a method in one of your models, say a Users model, you can do that in Dorguzen like so:

      use Dorguzen\Models\Users;

      $users = container(Users::class);

  or 

      $users = new \src\models\Users();

  then 

      $rules = [
          'email' => [
              'required',
              ['callback', [$users, 'checkIfInUse']]
          ]
      ];

Apart from one-line rules like required, or string, or integer etc, a rule should be followed by a colon, 
and then its value e.g. 

    min:8 
    
which means at least 8 characters are expected of that field, or 8 items if it is an array for example.
Multiple rules for a field are separated by pipe characters if they are defined as a string and not as an 
array e.g 

    $rules = [
          'field_name' => "rule1:value|rule2:value"
      ];
     


  Rules available (built-in)
  ---------------------------

    required — value must exist and not be empty

    nullable — allow null/empty to skip other checks

    sometimes — only validate when key present in data

    string — must be string

    integer / int — integer

    numeric — number or numeric string

    boolean — boolean-ish (0/1/true/false)

    email — RFC-ish email validation via filter_var

    url — filter_var URL

    min:x — for strings, numbers, arrays

    max:x — for strings, numbers, arrays

    between:x,y — for strings, numbers, arrays

    regex:pattern — pattern (without delimiters)

    in:one,two,three — value must be one of the list

    not_in:one,two — value must not be one of the list

    same:otherField — equal to other field

    different:otherField — not equal

    date — validateable by strtotime

    before:YYYY-MM-DD / after:YYYY-MM-DD

    array — must be array

    callback:callableName — call a callable


Also, the closure rules are allowed directly inside the rules array

You can extend the class with extra validate_* methods if you need special behaviour.



  Messages & translations
  ------------------------

You passed customMessages as second param to DGZ_Validator::make() or to $this->validator(). Keys can be 
either field.rule or just rule. The validator uses them in priority order.

Examples:

    $messages = [
        'email.required' => 'We need your email',
        'min' => 'Some field is too small'
    ];

    $validator = $this->validator($input, $rules, $messages);




  Performance & extension notes
  ------------------------------

Validation runs in PHP synchronously; it's fast for typical form payloads.
Keep complex checks (e.g., DB uniqueness) as callbacks that call model methods — they can be slower (DB roundtrip), 
so use them sparingly or add caching.

The 'sometimes' rule is handy for PATCH/partial updates.

For file validations you can add validate_mime or validate_max_filesize methods and call them from the same validator.



  Full controller example flow
  -------------------------------

    public function register()
    {
        $input = $_POST;

        $validator = $this->validator($input, [
            'username' => 'required|min:5|callback:\MyApp\User::usernameAvailable',
            'password' => 'required|min:8',
            'email' => 'required|email',
        ], [
            'username.callback' => 'That username is already taken.'
        ]);

        if ($validator->fails()) {
            return $this->render('auth/register', ['errors' => $validator->errors(), 'old' => $input]);
        }

        // create user...
    }



  
  Short tutorial 
  ----------------

Create validator instance

    $validator = $this->validator($_POST, ['email' => 'required|email', 'age' => 'nullable|integer|min:18']);

Check result

    if ($validator->fails()) { $errors = $validator->errors(); /* show errors */ }

Custom message

    $validator = $this->validator($data, $rules, ['email.required' => 'Please provide an email']);

Custom rule

Use closure:

    'username' => [
      'required',
      function($value, $all, $field) {
          return preg_match('/^[a-z0-9]+$/i', $value) ? true : 'Invalid username';
      }
    ]


Or callable:

    'slug' => 'required|callback:\App\Helpers::isSlugUnique'

API usage

    Validate json_decode(file_get_contents('php://input'), true) and return 422 with $validator->errors().

Extending rules

 Add protected validate_customname(...) to DGZ_Validator. It will automatically be used for rule customname.




        Form submission and Controller validation example
        ---------------------------------------------------
  View with form
  --------------

        $form = new DGZ_Form();

        $form::open(
            'chooseCategory', 
            $this->controller->config->getFileRootPath().'data/test-process-form', 
            'post'); ?>

        <div class="col-md-12">
            <?php
            $form::input(
                'name', 
                'text', 
                [
                    'name' => 'name', 
                    'placeholder' => 'your name',
                    'class' => 'col-md-12 form-control'
                ]);
            ?>
        </div>

        <div class="col-md-12">
            <?php 
            $form::select(
                'category',
                [
                    'Phones' => [
                        'iphone' => 'Apple iPhone',
                        'samsung' => 'Samsung Galaxy',
                    ],
                    'Laptops' => [
                        'macbook' => 'MacBook Pro',
                        'lenovo' => 'Lenovo Thinkpad',
                    ],
                    'other' => 'Miscellaneous'
                ],
                ['iphone'], // pre-selected
                true,
                [
                    'name' => 'category', 
                    'class' => 'col-md-12 form-select', 
                ]
            ); ?>
        </div>

        <div class="form-group col-md-12">
            <?php
            $form::submit('submit', 'Save data', ['class' => 'btn btn-primary btn-sm ml-3']);
            ?>
        </div>
        <?php

        $form::close(); 



  Controller validation
  ----------------------
  As we can see from the Form::open() section, this line: 
  ($this->controller->config->getFileRootPath().'data/test-process-form') tells us that the form submission handler is 
  the src\controllers\DataController, in the method named testProcessForm().

  The form has only two fields 
    -name: where the user is expected to enter their name
    -category: where the user is expected to make a selection from a list of categories

  In the form handler method, which is DataController->testProcessForm(), here is how the form submission is 
  processed:

        public function testProcessForm()
        {
            $input = request()->post(); 

            $rules = [
                'name' => 'required|max:8',
                'category' => 'min:2' // user must choose at least two items from the select field
            ];

            $customMessages = [
                'name.required' => 'name is required my dawg!',
                'name.max:8' => '8 characters max, for name please',
                'category.min:2' => 'common man, pick at least two ok'
            ];

            $validator = $this->validator($input, $rules, $customMessages);

            if ($validator->fails()) {
                $errors = $validator->errors();

                $errorMsg = "";
                
                foreach ($errors as $key => $error)
                {
                    $errorMsg .= $error[0].'<br>';
                }

                // send back to view: 
                $this->addErrors($errorMsg, 'Validation failed');

                // redirect back to another controller & method
                $this->redirect('data', 'privacy');

                // or render a view and pass in the error data
            }

          $this->addSuccess('Submission was successful', 'Yay');

            $view = Dorguzen\Core\DGZ_View::getView('privacy', $this, 'html');
            $this->setPageTitle('Data privacy');
            $view->show();
        }






        DGZ_Validation Rules - full list with usage and Custom messages
        ----------------------------------------------------------------
    Absolutely — here is a **clean, comprehensive, copy-ready list** of all DGZ validation rules, each with:

* **What the rule does**
* **How to write the rule inside your `$rules` array**
* **How to write custom messages** for that rule

No tables. Pure list format with indentation, perfect for your notes app.

---

# ✅ **DGZ Validation Rules — Full List With Usage + Custom Messages**

---

## **1. required**

* Ensures the field is present and not empty.
* **Usage in rules:**
  `'username' => ['required']`
* **Custom message:**
  `'username.required' => 'Please enter a username.'`

---

## **2. email**

* Validates that the field contains a valid email address.
* **Usage:**
  `'email' => ['required', 'email']`
* **Custom message:**
  `'email.email' => 'Enter a valid email address.'`

---

## **3. numeric**

* Ensures the field is a number.
* **Usage:**
  `'age' => ['numeric']`
* **Custom message:**
  `'age.numeric' => 'Age must be a number.'`

---

## **4. min:X**

* Minimum length (for strings) OR minimum numeric value (if numeric).
* **Usage:**
  `'password' => ['min:6']`
* **Custom message:**
  `'password.min' => 'Password must be at least 6 characters.'`

---

## **5. max:X**

* Maximum length (strings) OR max numeric value.
* **Usage:**
  `'username' => ['max:20']`
* **Custom message:**
  `'username.max' => 'Username cannot exceed 20 characters.'`

---

## **6. between:min,max**

* Ensures the value length or number is between two values.
* **Usage:**
  `'age' => ['numeric', 'between:18,65']`
* **Custom message:**
  `'age.between' => 'Age must be between 18 and 65.'`

---

## **7. same:otherField**

* Ensures two fields match.
* **Usage:**
  `'password_confirmation' => ['same:password']`
* **Custom message:**
  `'password_confirmation.same' => 'Passwords do not match.'`

---

## **8. in:a,b,c**

* Ensures the value is one of a list.
* **Usage:**
  `'role' => ['in:admin,user,guest']`
* **Custom message:**
  `'role.in' => 'Invalid role selected.'`

---

## **9. not_in:a,b,c**

* Ensures the value is *not* one of a list.
* **Usage:**
  `'username' => ['not_in:root,admin']`
* **Custom message:**
  `'username.not_in' => 'This username is not allowed.'`

---

## **10. regex:/pattern/**

* Validates a value using a regex.
* **Usage:**
  `'username' => ['regex:/^[a-z0-9_-]+$/i']`
* **Custom message:**
  `'username.regex' => 'Only letters, numbers, hyphens, and underscores allowed.'`

---

## **11. url**

* Validates a proper URL.
* **Usage:**
  `'website' => ['url']`
* **Custom message:**
  `'website.url' => 'Enter a valid URL.'`

---

## **12. date**

* Checks if the value is a valid date string.
* **Usage:**
  `'start_date' => ['date']`
* **Custom message:**
  `'start_date.date' => 'Enter a valid date.'`

---

## **13. before:otherDateField**

* Ensures a date occurs before another date.
* **Usage:**
  `'start_date' => ['before:end_date']`
* **Custom message:**
  `'start_date.before' => 'Start date must be before end date.'`

---

## **14. after:otherDateField**

* Ensures a date occurs after another date.
* **Usage:**
  `'end_date' => ['after:start_date']`
* **Custom message:**
  `'end_date.after' => 'End date must be after start date.'`

---

## **15. boolean**

* Value must be true/false, 1/0, yes/no.
* **Usage:**
  `'active' => ['boolean']`
* **Custom message:**
  `'active.boolean' => 'Invalid boolean value.'`

---

## **16. array**

* Ensures the field is an array.
* **Usage:**
  `'tags' => ['array']`
* **Custom message:**
  `'tags.array' => 'Tags must be an array.'`

---

## **17. closure-based rule**

* For fully custom logic.
* **Usage:**

  ```php
  'username' => [
      'required',
      function($value, $all, $field) {
          if (!preg_match('/^[a-z0-9_-]+$/i', $value)) {
              return 'Only letters, numbers, - and _ allowed.';
          }
          return true;
      }
  ]
  ```
* **Custom message:**
  Defined inside the closure return string.

---

## **18. file**

* Ensures the uploaded field is a file.
* **Usage:**
  `'avatar' => ['file']`
* **Custom message:**
  `'avatar.file' => 'Please upload a valid file.'`

---

## **19. mimes:jpg,png,gif**

* Ensures uploaded file matches MIME types.
* **Usage:**
  `'avatar' => ['file', 'mimes:jpg,png']`
* **Custom message:**
  `'avatar.mimes' => 'Only JPG and PNG images allowed.'`

---

## **20. size:X**

* Validates file size in kilobytes.
* **Usage:**
  `'avatar' => ['size:2048']`
* **Custom message:**
  `'avatar.size' => 'File must be exactly 2MB.'`

---

## **21. min_size:X**

* Minimum file size (KB).
* **Usage:**
  `'avatar' => ['min_size:50']`
* **Custom message:**
  `'avatar.min_size' => 'File must be at least 50KB.'`

---

## **22. max_size:X**

* Maximum file size (KB).
* **Usage:**
  `'avatar' => ['max_size:2048']`
* **Custom message:**
  `'avatar.max_size' => 'File cannot exceed 2MB.'`

---

## **23. unique:table,column(optional)**

* Ensures the value doesn’t already exist in DB.
* **Usage:**
  `'email' => ['unique:users,email']`
* **Custom message:**
  `'email.unique' => 'This email is already registered.'`

---

## **24. exists:table,column(optional)**

* Ensures a value already exists in DB.
* **Usage:**
  `'country_id' => ['exists:countries,id']`
* **Custom message:**
  `'country_id.exists' => 'Invalid country selected.'`

---

## **25. nullable**

* Allows field to be null and skip other validation unless a value is provided.
* **Usage:**
  `'middle_name' => ['nullable', 'min:2']`
* **Custom message:**
  Works with whatever rule(s) follow.

---

# Want me to generate a **nice documentation-ready version** for your official DGZ docs?

I can format it in your usual DGZ documentation style, or even generate the full HTML/Markdown page for you.








//——————————
DDDDDDO 
//----------—————
Data types, Data structures, Design patterns, DB, Dates, Frontend/UI and Packages and Request handling, OOP

——————————————————————
	DATA TYPES
——————————————————————

  -Intro
  -Why data types differ across languages
  -Similarities across languages
  -Understanding primitive and reference 
     types
         -Primitive types
             -Why the name “primitive”?
             -Why the Name “Reference”?
         -Reference types
  -Understanding strong and weak typing
         1) Strongly typed languages
         2) Weakly typed languages
  -Understanding static and dynamic typing
        1) Static typing
        2) Dynamic typing
  -The best way to study data types
  -Booleans
     -The different interpretations of true or 
       falsee
  -DATA TYPES IN YOUR SPECIFIC LANGUAGE HERE


    In computer programming, a data type is 
  a classification identifying one of various 
  types of data, such as floating-point, 
  integer, or Boolean, that determines the 
  possible values for that type; the 
  operations that can be done on values of 
  that type; the meaning of the data; and the 
  way values of that type can be stored. 
  Programming languages allow you to also 
  convert data from one type to another. 
    If this is not initially clear to you, do not 
  worry, you are not alone. I have got you 
  covered on this-it will all become 
  clear in a minute.

    The concept of data types can be confusing for new programmers. When i started out, the sources of information i consulted, which were mostly books and online materials did not seem to correlate. The data types (primitives and reference types) in Java, seemed to be different from data types in python, and those in Javascript also seemed different. The more i tried to piece the data together to make sense, the more some questions came to mind, which i found so many developers were wondering about too. The first question was whether the concept of data types is programming-language-agnostic, or if they are different for each language. Secondly, i wanted to know the best way to study data types. Do you have to study them for each programming language, or is it possible to master the topic in a way that applies correctly to all languages? I finally got the answers. Let’s talk about what i found.
  The concept of data types exists in all programming languages, but how they are implemented and categorized can differ between languages. Broadly, data types define what kind of data a variable can hold, but the specific details of types—especially primitive types—can vary across languages.


Why data types differ across languages
—————————————————-

-1) Design Philosophy: Different languages 
     have different goals. For example, Java is 
     a statically typed language, meaning 
     data types are explicitly declared and 
     checked at compile-time. Python, on the 
     other hand, is dynamically typed, 
     meaning data types are determined at 
     runtime.
-2) Memory Management: Some languages, 
     like Java, provide primitive types (e.g., 
     int, float) that are more memory-efficient 
     compared to reference types. Python 
     treats everything as an object, which 
     changes how it handles data types.
-3) Typing System: JavaScript has weak 
     typing (types are coerced), Python is 
     dynamically typed, and Java is strongly 
     typed with clear distinctions between 
     primitive types and reference types.


Similarities across Languages
———————————-

    Even though the specifics differ, the high-
  level concept of data types is similar 
  across languages:

-1) Primitive Types: These usually include 
     basic data types like integers, floats (or 
     doubles), booleans, and characters. 
     Some languages extend this with 
     additional types (e.g., long in Java or 
     bigint in JavaScript).
-2) Reference Types: These refer to more 
     complex data structures like arrays, 
     objects, lists, or dictionaries. While the 
     syntax and underlying implementation 
     differ, the idea of reference types is 
     shared across most languages.



   Understanding primitive and reference 
     types
   ———————————————————
    Sure! Let'----------—————s break down primitive types 
  and reference types in simple terms.

   a) Primitive Types
      Primitive types are the basic building 
      blocks of data in a programming 
      language. They are called 'primitive' 
      because they are the most simple, low-
      level data types, and they are not made 
      up of other types.

      Key Features:
           -They directly store the value.
           -They are generally fixed-size and 
             take up a predictable amount of 
             memory.
           -They are faster to access and 
             manipulate because they are stored 
             in a simple form.
           -They are immutable, meaning once 
             you assign a value to them, it can't 
             change (in languages like Java; 
             though in Python, immutability 
             applies to some, but not all).

      Why the Name “Primitive”?
      ——————————
    They are called primitive because they 
  are the basic, simple types that can't be 
  broken down further. Think of them like 
  "atoms" in programming.

  Examples of Primitive Types:
        -Integer types
        -Numbers without a decimal (e.g., `int`, 
              `short`, `long`, `byte` in Java).
        -Floating-point types: Numbers with 
               decimals (e.g., `float`, `double`).
        -Character type: Single characters 
               (e.g., `char` in Java).
        -Boolean type: Represents true/false 
               values (e.g., `boolean` in Java).
        -Byte: A data type that holds a small 
               integer value (e.g., in Java, `byte` 
               can store values from -128 to 127).

         In Python and JavaScript, these are 
         similar, but types like `int` or `float` 
         are automatically managed and are a 
         bit more flexible. For example:
             -Python: `int`, `float`, `bool`
             -JavaScript: `number`, `boolean`

   b) Reference Types
      Reference types are more complex data 
      types that store a reference (or 
      address) to the actual data rather 
      than the data itself. They are often made 
      up of multiple primitive types or other 
      reference types.

     Key Features:
          -They store the location (reference) of 
            the data, not the actual data.
          -They can be larger and more 
            complex because they hold more 
            than one value.
          -They are often mutable, meaning the 
            data they reference can be changed.
          -They are slower to access because 
            you first need to look up where the 
            data is stored in memory.

      Why the Name “Reference”?
      ———————————————
    They are called reference types because 
  instead of directly storing the value, they 
  store a reference (like an address) to 
  where the actual value or object is located 
  in memory.

  Examples of Reference Types:
        -Arrays: Collections of items of the 
          same type (e.g., `int[]` in Java, `list` 
          in Python).
        -Objects: Complex types made of 
          properties (e.g., custom objects in 
          Java, dictionaries or class instances in 
          Python).
        -Strings: In some languages like Java, 
          `String` is a reference type, because it 
          is an object, not a primitive.
        -Lists: In Python, lists are reference 
          types because they store a reference 
          to where the list items are held in 
          memory.
        -Functions: In JavaScript, functions are 
          treated as reference types because 
          they are objects.

    Here are the key differences between 
  primitive and reference types
    Primitive types: 
        Directly store values, like a box that 
        holds a number.
    Reference types: 
        Store the location of the data, like an 
        index card that points to where the 
        actual data is stored.

  Why does the distinction matter?
    When you work with 'primitive types', you 
    are manipulating the actual value. With     
    reference types, you are manipulating the 
    reference (pointer) to the data. This 
    difference affects how variables behave, 
    especially when passing them to 
    functions or copying them.




     Understanding strong and weak typing
     ——————————————————
    The concepts of 'strongly typed' and
  'weakly typed' refer to how strictly a 
  programming language enforces the rules 
  around data types.

  1) Strongly typed languages
  ————————————
    In a strongly typed language, the type of 
  a variable is enforced, meaning you cannot 
  freely mix and match different data types 
  without following specific rules or explicitly 
  converting them.

  Key Points:
     -You have to be careful with how you use 
       variables.
     -Data types are strictly checked, and 
       there is no type coercion (the
       language doesn't automatically convert 
       one type to another if it doesn't make 
       sense).
     -If you want to use data in a way that 
       changes its type, you need to explicitly 
       convert it.

  Examples
       Java:
              int num = 5;
              String text = "Hello";

              // Trying to combine them without 
              // converting would throw an error

              // Error: incompatible types
              String result = num + text; 

             // You must convert 'num' to a string
              // Now it works: "5Hello"
             String result = 
                       String.valueOf(num) + text; 

            So, in Java, you can't mix an integer    
            and a string without first converting 
            the integer to a string. The language 
            forces you to respect the types 
            you're working with.

    Python:
            age = 25
            name = "John"

            # This will cause an error in Python 
            # because you cannot concatenate a    
            # string with an integer directly
            # Error: TypeError
            result = name + age 

           # You need to explicitly convert 'age' 
           # to a string
           # Now it works: "John25"
           result = name + str(age) 


  -2) Weakly typed languages
  ———————————————
    In a weakly typed language, the language 
  is more flexible and will try to convert data 
  types automatically when needed, even if it 
  doesn't always make perfect sense. You 
  would sometimes hear people talk of 
  loosely-typed languages. When they do, 
  they are generally talking about weakly 
  typed languages. Both terms are often 
  used interchangeably

  Key Points:
     -Data types are not strictly enforced.
     -The language might automatically 
       convert one type to another (a process 
       called type coercion).
    -You can mix types more freely, but it 
      might lead to unexpected behavior or 
      results.

  Examples:
        JavaScript
                let num = 5;
                let text = "Hello";

              // JavaScript will automatically 
              // convert the number to a string 
              // and combine them
              let result = num + text;  // "5Hello"

             // Another example:
             // the value of sum will be "510", 
             // not 15
            let sum = "5" + 10; 

           So, in JavaScript, when you try to 
           combine a number with a string, the 
           number is automatically converted to 
           a string, and the result is `"5Hello"`. 
           This automatic conversion is known 
           as type coercion and is a hallmark of 
           weak typing.

  Recap:
      -Strongly Typed languages require you 
        to be explicit about data types and 
        perform conversions yourself. Errors 
        occur if you try to mix types improperly 
        (e.g., combining a string with an 
        integer).
     -Weakly Typed languages are more 
       relaxed and try to convert types 
       automatically, which can be convenient 
       but might sometimes lead to 
       unexpected behaviour (e.g., `"5" + 10` 
       results in `"510"` instead of `15`).


  Understanding static and dynamic typing 
  ————————————————————-
    The difference between static typing and
  dynamic typing is about when the type of a 
  variable is determined and whether you 
  need to declare it explicitly.

  1) Static Typing
  —————-
    In statically typed languages, you must 
  declare the type of a variable when writing 
  the code, and the type is checked at 
  compile time (before the program runs).

  Key Points:
       -The type is known and fixed when the 
         code is written.
       -The compiler checks for type errors 
         before the program runs, preventing 
         some bugs.
       -You cannot change the type of a 
         variable after it's declared.

  Example 
       Java:

           // You must declare the type as 'int'
           int age = 25; 

          // Trying to assign a different type later 
          // will cause an error
          // Error: incompatible types
          age = "twenty-five"; 

         In Java, the type of ‘age’ is declared 
         as ‘int’, and the compiler won’t let you 
         assign a string to it later.

  2) Dynamic Typing
  ————————
    In dynamically typed languages, you do 
  not need to declare the typeof a variable, 
  and the type is determined at runtime
  (while the program is running).

  Key Points:
       -The type is figured out as the program 
         runs.
       -You can assign a value of any type to a 
         variable, and change its type later if 
         needed.
       -The flexibility comes at the cost of 
         potential runtime errors.

  Example 
         Python

            # No need to declare the type
            age = 25 

            # You can change the type of 'age' at 
            # any point
            # Works fine; now it's a string
            age = "twenty-five" 

           In Python, you don't declare the type 
           of 'age' upfront. You can assign an 
           integer, and later assign a string to 
           the same variable without any issues
           —until the program runs and an error 
           might occur.

  Recap:
       -In Static Typing the variable type is 
         declared and fixed when you write the 
         code (before running the program). 
         Example: Java, C.
       -In Dynamic Typing the variable type is 
         determined at runtime, and it can 
         change as the program runs. Example: 
         Python, JavaScript.



   The best way to study data types
   —————————————————
    You should do this in two steps; mastering 
  the general core concepts that apply to all 
  languages, then learn the individual 
  language implementations. This way, you'll 
  build a strong foundation and can easily 
  switch between languages by learning 
  their specific syntax and behaviour around 
  data types. Let us break down the two 
  steps.

  -1) Understand the language-agnostic 
        core concepts. This involves properly
        understanding:
        a) Primitive vs. Reference Types: Grasp 
             the idea of primitives (basic, low-
             level types) vs. reference types 
             (complex, stored by reference).
	 b) Strong vs. Weak Typing: Understand 
            the difference between strongly 
            typed (Java, C++) and weakly typed 
            (JavaScript) languages. See the in-
            depth explanation on strong vs weak 
            typing later below.
	 c) Static vs. Dynamic Typing: Get 
            comfortable with the idea of static 
            typing (e.g., Java, where types are 
           checked at compile time) vs. dynamic 
           typing (e.g., Python, where types are 
           checked at runtime). See the in-
           depth explanation on static and 
           dynamic typing later below.

 -2) Study language-specific.   
        implementations.
        Once you understand the core 
        concepts, focus on how individual 
        languages implement these concepts. 
        This will help you adapt to the specifics 
        of any language while keeping the big 
        picture in mind. When learning a 
        language, make sure you:
	
     a) Study its primitive types and 
          understand how it handles more 
          complex data structures.
     b) Learn about its memory management 
          and how it treats data types internally 
          (e.g., Java uses objects for everything 
          except primitives, while Python treats 
          everything as an object).

    Finally, find out if the language you are 
  learning offers ways to do type casting, 
  and if it has any built-in functions for 
  converting data types.

    We have pretty much covered the first 
  step in the notes above, where we 
  mastered the core concepts by 
  theoretically examining all the facets of 
  data types. This involved learning about 
  primitive vs reference types, strong vs 
  weak types, and static vs dynamic types 
  which are programming-language 
  agnostic. We just have step 2 to cover. You 
  will do step 2 on your own whenever you 
  pick up a new language to learn. You just 
  have to make sure you learn of how it 
  implements primitive types as well as 
  reference types-which are more complex 
  data structures.


BOOLEANS
———————
A boolean is a data type that has two possible values, true or false, which can also be written as TRUE or FALSE.

In PHP, `true` and `false` (in lowercase) are the boolean literals representing the two possible boolean values. The uppercase versions, `TRUE` and `FALSE`, are exactly the same as `true` and `false` with no functional difference between them. They are completely interchangeable and treated the same by PHP
  
The lowercase versions are the more commonly used and recommended forms. They align with common coding standards and are more readable because most PHP keywords are in lowercase.
  
           $is_valid = true;
           if ($is_valid) {
               echo "Valid!";
            }

The uppercase equivalents (TRUE and FALSE) are less commonly used. Some developers use them for stylistic reasons, historical conventions, or to match coding styles from other programming languages or frameworks.

               $is_valid = TRUE;
               if ($is_valid) {
                   echo "Valid!";
               }


The different interpretations of true or false
——————————————————
    In the context of booleans, there are 4 
  data constructs that evaluate in code to 
  false in a boolean context, but they 
  however represent different types and 
  values. Understanding their differences is 
  important for handling them correctly in 
  your code These are:

        -i) NULL (same as null). This is the 
             absence of a value, or a variable 
             with no value assigned to it.
        -ii) A blank string (“”)
        -iii) An empty array ([])
        -iv) 0 or 0.0

    The opposite of the above constructs will 
  make them evaluate to true. For example, 
  the presence of a value instead of NULL, 
  or instead of a blank string, or adding a 
  new item to the empty array, or changing the 
  value from 0 (or 0.0) to 1 or any unsigned 
  number (1 and above) will make them 
  evaluate to true.

    There are times when you want to make 
  absolutely sure that you are working with a 
  specific data type, for example an array, or 
  a NULL. You may want to know not only 
  that your data is false, but you want to 
  know if the data is an empty array. In this 
  case, we need some way in code to 
  distinguish between the data types and 
  identify them.
    There are ways for you to do this. The 
  easiest is to use the identical operator   
  '==='. So, for example, when working with a 
  an array stored in a variable $myData, 
  instead of saying 

         if ($mySata == false)
         {
             // $myData is false, but we do not 
             // know what data type it is
         }

  do this:

         if ($mySata === [])
         {
             // $myData is an empty array, 
             // so it is false
         }

    Besides, the identical operator for strict 
  comparison, there are some handy php 
  built-in functions which you can use to that 
  effect.
  Here is how to distinguish between false 
  and the other data types given above:

-1) NULL (or `null`)
   NULL represents the absence of a value or 
   a variable with no value assigned.
   Example:
     
     $var = null;
     if ($var === null) {
         echo "The variable is NULL.";
     }
     
   -Distinguishing: You can use the strict 
     comparison (`===`) to check if a variable 
     is `NULL`.

-2) Empty String ("")
   An empty string is a string with no 
   characters. It is considered `false` when 
   evaluated in a boolean context.
   Example:
     
     $var = "";
     if ($var === "") {
         echo "The variable is an empty string.";
     }
     
   -Distinguishing: Use the strict comparison 
     (===) to check if a variable is an empty 
     string.

-3) Empty Array ([] or array())
   An empty array is an array with no 
   elements. It evaluates to `false` in a 
   boolean context.
   -Example:
     
     $var = [];
     if ($var === []) {
         echo "The variable is an empty array.";
     }
     
   -Distinguishing: Use the strict comparison 
     (===) to check if a variable is an empty 
     array.

-4) Zero (0 or 0.0)
   The integer `0` or the float `0.0` 
    evaluates to `false` in a boolean context.
   Example:
     
     $var = 0;
     if ($var === 0) {
         echo "The variable is zero.";
     }
     
   -Distinguishing: Use the strict comparison 
     (===) to check if a variable is `0` or 
     `0.0`.




———DATA TYPES IN YOUR SPECIFIC LANGUAGE HERE ————
————————————————-




——————————————————————
	DATA STRUCTURES
——————————————————————

  Intro
  Data Structures and Data Types
  List of data structures
     -Arrays
     -Linked lists
     -Stacks 
     -Queues
     -Tuples
     -Dictionaries (Maps/HashMaps)
     -Sets
     -Structs
     -Trees (Binary Trees, AVL Trees, etc)
     -Collections



    The topic of data structures is like an 
  extended study of data types. When we 
  studied data types; we learned about the 
  two groups that make them-primitive and 
  reference types. Data structures are 
  closely related to reference types in 
  programming because they often deal with   
  collections of data and more complex data 
  management, as opposed to the simple, 
  single-value nature of primitive types.

 Data Structures and Data Types
 —————————-
    We know that where programming 
  languages are similar when it comes to 
  handling data types is in the fact that they 
  all have the core concepts of primitive and 
  reference types. They only differ in the way 
  they implement these concepts.
    Before we introduce data structures, let 
  us briefly remind ourselves of what 
  primitive types are again. Primitive types 
  are the simplest, indivisible data types that 
  store single values. Examples are integers, 
  booleans, and characters. They don't 
  involve any structure or organization 
  beyond the basic storage of a single piece 
  of data. There is the other group known as 
  reference types which much closer to data 
  structures. Let us see why.
    Data structures are ways of organizing 
  and storing data so that they can be 
  accessed and modified efficiently. Since 
  data structures often involve collections of 
  data (e.g., lists, trees, hash tables), they 
  need to store references to multiple pieces 
  of data, rather than the actual values 
  themselves. This is why data structures are 
  typically implemented using reference 
  types. Next, let us look at how data 
  structures use reference types.
    Reference types hold the address (or 
  reference) to the actual data in memory. 
  When you work with data structures like 
  arrays, lists, or dictionaries, you're dealing 
  with references to elements, not the actual 
  values themselves.
    Mutable or Immutable. Many data 
  structures allow you to change (mutate) 
  the data they contain. This is possible 
  because reference types can point to new 
  or modified data without changing the 
  variable itself. For instance, in a list, you 
  can update individual elements without 
  changing the whole structure. Also, with 
  classes, you can change the property of an 
  object without changing that data on the 
  class where the object came from.
    Some examples of data structures are:
        -Arrays/Lists:
           They are collections of items stored in 
            contiguous memory. Each element in 
            the array is accessed through its 
            reference (i.e., the index points to the 
            location of the data).
        -Dictionaries/Maps:
            They store key-value pairs where 
             each value is referenced by a key. 
             The actual values and keys are 
             reference types.
        -Trees:
             Trees are hierarchical structures 
             where each node references its child 
             nodes, creating a complex network 
             of relationships between the data.
        -Stacks/Queues:
             These are collections of data 
             organised in a specific order (e.g., 
             Last In, First Out for stacks). Each 
             item in the structure is referenced, 
             making it easy to manage more 
             complex data flows.

    So, while primitive types represent single,     
  indivisible data points (e.g., an integer or a 
  character), data structures are complex 
  ways of organizing multiple pieces of data 
  and are built using reference types, 
  because they need to handle multiple 
  values, often of varying or unknown sizes, 
  efficiently. In essence, data structures rely 
  on reference types to manage and 
  manipulate collections of data, while 
  primitive types serve as the fundamental 
  building blocks for individual data 
  elements.


      List of data structures
      ———————————-
    With that said, we are ready to study the 
  different data structures. There are some 
  things to keep in mind. Some data 
  structures exist in certain languages but do 
  not exist in others. For example, 
  dictionaries exist in Python but not in PHP. 
    Some data structures come built into 
  a programming language, while in other 
  languages, you would have to create them 
  yourself in code. 
    When studying each data structure, make 
  sure with each, you are studying how the 
  individual language you are learning 
  handles that in memory, and that you are
  understanding the weaknesses and 
  strengths of the data structure. This will 
  help you know which one to use in which 
  scenario when solving problems in 
  programming.
    We will proceed with the list if data 
  structures by listing each one and 
  examining everything about it from which 
  language uses it, how is it implemented, 
  what kind of problems it can solve, what 
  limitations it has etc. We will look at only 
  the most popular structures. With this 
  guidance, you will be able to pick any data 
  structure from any new language you are 
  learning and master them, if we have not 
  covered them here already.
    The focus of code examples will be in 
  Python, JavaScript and PHP, except that 
  data structure does not exist in any of 
  these three languages-in which case i will 
  give a code example in the relevant 
  language.


           ARRAYS
           ————
    Arrays are a collection of elements of the 
  same data type, stored in contiguous 
  memory locations. They provide quick 
  access to elements using an index and are 
  a foundational structure in most 
  programming languages.
  
   Languages: PHP, JavaScript, Python
     Implementation: Stored in contiguous 
         memory, accessed by index.
   Problems it solves: Efficient storage and 
         access of fixed-size data collections.
   Limitations: Fixed size in some languages 
         (e.g., arrays in JavaScript can be 
         resized dynamically).

       Python example:
           # Creating an array (list in Python)
           arr = [1, 2, 3, 4]

       JavaScript example:
           // Creating an array in JavaScript
           let arr = [1, 2, 3, 4];

       PHP example:
           // Creating an array in PHP
          $arr = array(1, 2, 3, 4);
  

  
          LINKED LISTS	
	—————————

    Linked lists are data structures consisting 
  of nodes where each node contains a 
  value and a pointer to the next node. They 
  allow dynamic resizing but require 
  traversal to access elements.
  
     Languages: Python, PHP (manually 
           implemented), JavaScript (manually 
           implemented)
     Implementation: A series of nodes, where 
           each node points to the next.
     Problems it solves: Efficient insertion and 
           deletion, especially in the middle of 
           the list.
      Limitations: Slower access compared to 
            arrays due to the need to traverse 
            nodes.

         Python example
         ———————
        # Simple linked list node class in Python
        class Node:
               def __init__(self, data):
                       self.data = data
                       self.next = Non

        JavaScript example
        —————————-
        // Linked list implementation in 
        // JavaScript
        class Node {
                   constructor(data) {
                          this.data = data;
                          this.next = null;
                   }
          }
 

        PHP example
        ———————
        // Linked list implementation in PHP
        class Node {
            public $data;
            public $next;

            public function __construct($data)      
            {
                     $this->data = $data;
                     $this->next = null;
             }
        }
  


          STACKS
          ————-
    Stacks are LIFO (Last In, First Out) data
  structures, meaning that the last element 
  added is the first to be removed. They are 
  useful for operations such as undo 
  functionality or managing function calls in 
  recursion.
  
     Languages: PHP, JavaScript, Python
     Implementation: Can be implemented 
           using arrays or linked lists.
     Problems it solves: Undo operations, 
           function call management, and 
           backtracking.
     Limitations: Restricted access (only the 
            top element can be accessed).

     Python example
     ————————-
     # Stack implemented using list in Python
     stack = []
     stack.append(1)  # Push
     stack.pop()      # Pop
  

    JavaScript example
    ——————————-
    // Stack using an array in JavaScript
    let stack = [];
    stack.push(1);    // Push
    stack.pop();      // Pop

    PHP example
    ———————
    // Stack using an array in PHP
    $stack = array();
    array_push($stack, 1);  // Push
    array_pop($stack);      // Pop
  

             QUEUES
             —————

    Queues are FIFO (First In, First Out) data 
  structures, meaning that the first element 
  added is the first to be removed. They are 
  commonly used for task scheduling and 
  handling ordered data processing.
  
      Languages: Python, PHP, JavaScript
      Implementation: Implemented using 
             arrays or linked lists.
      Problems it solves: Ideal for handling 
             tasks in the order they are added 
             (e.g., print jobs, task scheduling).
      Limitations: Restricted access (only front 
              and rear elements can be 
              accessed).

      Python example
      ————————-
      # Queue implemented using deque in     
      # Python
      from collections import deque
      queue = deque()
      queue.append(1)  # Enqueue
      queue.popleft()  # Dequeue
  

      JavaScript example
      ——————————
      // Queue implemented using array in   
      // JavaScript
      let queue = [];
      queue.push(1);    // Enqueue
      queue.shift();    // Dequeue
  

      PHP example
      ———————-
      // Queue using an array in PHP
      $queue = array();
      array_push($queue, 1);  // Enqueue
      array_shift($queue);    // Dequeue
  


             TUPLES
             ————-

    Tuples are immutable, ordered collections 
  of elements, often of different types. They 
  are useful for returning multiple values 
  from functions or creating fixed records of 
  data.
  
        Languages: Python (native tuples), 
               JavaScript, PHP (as array-like 
               structures)
       Implementation: Immutable collections, 
               often used for storing multiple 
               related values.
       Problems it solves: Useful for returning 
              multiple values from functions, or 
              representing fixed groups of values.
       Limitations: Immutable, meaning you 
              cannot modify them once created.

       Python example:
       ————————-
            # Tuple in Python
            tup = (1, "apple", 3.14)

      JavaScript example:
      —————————
           // No native tuple in JavaScript, arrays 
           // can be used for similar purposes
           let tup = [1, "apple", 3.14];
  
      PHP example:
      ———————-
           // PHP doesn't have native tuples, but 
           // you can use arrays
           $tuple = array(1, 'apple', 3.14);



      Dictionaries (Maps/HashMaps)
      ————————————————
    Dictionaries, or hash maps, store data in 
  key-value pairs. They offer fast lookup, 
  insertion, and deletion by key and are used 
  for tasks such as caching or implementing 
  key-value stores.
  
        Languages: Python, PHP, JavaScript
        Implementation: Typically implemented 
               with hash tables.
        Problems it solves: Fast data retrieval 
               based on unique keys, useful for 
               tasks like lookups, caching, and 
               indexing.
        Limitations: Keys must be unique, 
               because hash collisions can affect 
               performance.

        Python example:
       ————————-
       # Dictionary in Python
       my_dict = {'name': 'Alice', 'age': 30}
  
      JavaScript example:
      ——————————-
      // Object as dictionary in JavaScript
      let my_dict = {'name': 'Alice', 'age': 30};
  

      PHP example:
      ———————-
      // Associative array in PHP (like a 
      // dictionary)
      $my_dict = array('name' => 'Alice', 'age'   
           => 30);
  


                  Sets
                ————
    Sets are unordered collections of unique
  elements. They are commonly used for 
  removing duplicates or performing set 
  operations like union or intersection.
  
        Languages: Python, JavaScript, PHP   
               (array can be used)
        Implementation: Typically backed by a 
               hash table or balanced tree.
        Problems it solves: Efficient 
               membership testing, removing 
               duplicates.
        Limitations: Unordered and doesn't 
               allow duplicate values.

        Python example:
               # Set in Python
               my_set = {1, 2, 3, 4}
  
        JavaScript example:
               // Set in JavaScript
              let my_set = new Set([1, 2, 3, 4]);
  
        PHP example:
             // No native set type in PHP, but 
             // arrays can be used
             $my_set = array_unique(array(1, 2, 
                   3, 4));
  

                   Structs
               ———————
    Structs are user-defined data types that 
  group different types of data together. 
  They are commonly used in languages like   
  C and Go to create records of related 
  fields.
  
        Languages: C, Go
        Implementation: A collection of fields 
              that group data types together.
        Problems it solves: Useful for grouping 
              related data like a simple record.
        Limitations: No built-in methods like 
              objects in OOP languages.

        Go example:
               // Struct in Go
               type Person struct {
                    name string
                    age  int
               }
  


         Trees (Binary Trees, AVL Trees, etc.)
         —————————————————— 
    Trees are hierarchical data structures 
  that store data in nodes, with each node 
  having child nodes. They are useful for 
  representing hierarchical data like file 
  systems or organisational structures.
  
        Languages: Python, PHP, JavaScript
        Implementation: Nodes that point to 
               child nodes.
        Problems it solves: Efficient searching 
               and sorting of hierarchical data, 
               used in things like file systems or 
               databases.
         Limitations: Trees can become 
               unbalanced, leading to inefficient 
               operations.

         Python example:
                # Binary tree node in Python
                class Node:
                        def __init__(self, data):
                               self.data = data
                               self.left = None
                               self.right = None

                # Example usage
                root = Node(10)
                root.left = Node(5)
                root.right = Node(20)
  

         JavaScript example:
                // Binary tree node in JavaScript
                class Node {
                       constructor(data) {
                              this.data = data;
                              this.left = null;
                              this.right = null;
                       }
                }

               // Example usage
               let root = new Node(10);
               root.left = new Node(5);
               root.right = new Node(20);

 
        PHP example
              // Binary tree node in PHP
              class Node {
                      public $data;
                      public $left;
                      public $right;

                  public function  
                      __construct($data) {
                              $this->data = $data;
                              $this->left = null;
                              $this->right = null;
                      }
                }

           // Example usage
           $root = new Node(10);
           $root->left = new Node(5);
           $root->right = new Node(20);

        In these examples, we created a simple 
      binary tree with a root node and two 
      children (left and right). This basic 
      structure can be extended to support 
      more complex tree operations such as 
      traversal, insertion, and deletion.



                COLLECTIONS
                ———————-
    Collections are a broad category of data 
  structures that provide a way to group 
  multiple values into a single entity. They 
  abstract away how the data is stored and 
  offer various operations for adding, 
  removing, or modifying the data. They can 
  store data of the same type (homogeneous 
  collections) or different types 
  (heterogeneous collections).

         Languages: Python, JavaScript, PHP 
                (Collections can be implemented 
                using arrays, sets, dictionaries, 
                etc.)
         Implementation: Collections can be 
                implemented as arrays, 
                dictionaries, sets, and other 
                structures, each with specific 
                behaviours and performance 
                characteristics.
         Problems it solves: Efficient storage, 
                retrieval, and manipulation of 
                multiple elements in a structured 
                way.
         Limitations: Depending on the 
                collection type, some may impose 
                limitations like immutability, lack of 
                random access, or performance 
                constraints for certain operations.


        Python example:
              # Collections in Python (using the 
              # collections module)
              from collections import deque, 
                   defaultdict

              # Deque (Double-ended queue) as 
              # a collection
              my_deque = deque([1, 2, 3])

              # Add to the right
              my_deque.append(4) 

              # Add to the left
              my_deque.appendleft(0) 

              # Defaultdict as a collection that   
              # defaults to list
              my_dict = defaultdict(list)
              my_dict['key'].append('value')

        JavaScript example:
        ——————————
           // Collections in JavaScript (using 
           // Array, Map, Set)
           let arr = [1, 2, 3];  // Array collection
          arr.push(4);

          // Map collection
          let myMap = new Map(); 
          myMap.set('key', 'value');

          // Set collection
          let mySet = new Set([1, 2, 3]); 
          mySet.add(4);


      PHP example:
      ———————-
          // Collections in PHP (using arrays,        
          // collections class from Laravel for 
          // example. An Array collection
         $my_array = array(1, 2, 3); 
         array_push($my_array, 4);

          // Associative array as a collection
          $my_assoc_array = 
                          array('key' => 'value'); 

          // Using the Laravel Collections class 
          // (if using Laravel)
          use Illuminate\Support\Collection;

          $collection = collect([1, 2, 3]);
          $collection->push(4);

          In summary, Collections provide a 
          flexible way to manage data. In    
          Python, the 'collections' module offers 
          specialised data structures like 
          'deque' and 'defaultdict'. In JavaScript, 
          collections can be implemented using 
          arrays, maps, or sets, depending on 
          the need. PHP provides arrays, and 
          frameworks like Laravel offer more 
          robust collection handling. 

     Limitations: Some collections like sets 
           only store unique values, while others, 
           like arrays, have ordered but slower 
           search performance.




——————————————————————
	DESIGN PATTERNS
——————————————————————

    Design patterns are reusable solutions to 
  common software design problems. They 
  provide a way to solve issues in your 
  code's structure while promoting 
  maintainability and scalability. Design 
  patterns are categorised into three groups:

   1) Creational Patterns
        These are concerned with object 
         creation.

         -SINGLETON
         -FACTORY
         -ABSTRACT FACTORY
         -BUILDER
         -PROTOTYPE
   2) Structural Patterns
         These patterns focus on the 
          composition (structure) of classes or 
          objects.

         -ADAPTER PATTERN
         -DECORATOR PATTERN
         -FACADE PATTERN
         -COMPOSITE PATTERN
         -PROXY PATTERN
   3) Behavioral Patterns
          These patterns deal with object 
          interaction and responsibility 
          distribution.

         -OBSERVER PATTERN
         -STRATEGY PATTERN
         -TEMPLATE METHOD PATTERN
         -COMMAND PATTERN
         -ITERATOR PATTERN


    These design patterns are global and 
  programming-language agnostic, however, 
  each programming language has its own 
  way of implementing them. In the 
  explanations of all these design patterns 
  below, i provide examples in PHP. If you 
  program in another language, you just 
  have to learn how each of the patterns are 
  implemented in that language. I can assure 
  you that the approach is generally always 
  the same, with syntactical differences of 
  course.

         1) Creational Design Patterns
         ————————————-
    Creational design patterns deal with 
  object creation mechanisms, trying to 
  create objects in a manner suitable to the 
  situation. These patterns provide flexibility 
  in how objects are instantiated and 
  constructed. By using these creational 
  patterns, you can better manage object 
  creation processes in your applications, 
  ensuring efficiency, flexibility, and 
  maintainability.
    The following are the patterns in this 
  group:

        -SINGLETON
        -FACTORY
        -ABSTRACT FACTORY
        -BUILDER
        -PROTOTYPE


    SINGLETON PATTERN
    —————————
    The singleton pattern ensures that a class 
  has only one instance and provides a 
  global point of access to it. It's commonly 
  used for shared resources like databases 
  or logging.
  
    PHP example
    ———

          class DatabaseConnection {
                // Static property to hold the single   
                // instance
                private static $instance = null;
  
               // Prop to hold the PDO connection
               private $connection;
  
              // Database connection credentials
              private $host = 'localhost';
              private $db_name = 'my_database';
              private $username = 'root';
              private $password = 'password';

              // Constructor is private to prevent 
              // direct creation of object
              private function __construct() {
                    try {
                          // Create the database 
                          // connection using PDO
                          $this->connection = 
                               new PDO(
                                 "mysql:host=$this->host;
                             dbname=$this->db_name", 
                                 $this->username, 
                                 $this->password);
      
                         $this->connection
                             ->setAttribute(
                       PDO::ATTR_ERRMODE,      
                       PDO::ERRMODE_EXCEPTION);
                      } 
                      catch (PDOException $e) {
                        // Handle any connection error
                        echo "Database connection 
                           failed: " . $e->getMessage();
                      }
                 }

                // Public static method to get the 
                // single instance of the class
                public static function getInstance()    
                {
                   // If no instance exists, create one
                   if (self::$instance === null) {
                       self::$instance 
                         = new DatabaseConnection();
                    }
    
                    // Return the single instance
                    return self::$instance;
                }

              // Method to get the PDO connection
              public function getConnection() {
                  return $this->connection;
              }
       } // End of singleton class

      Using the Singleton to connect to the 
      database
      ————————————

      // Get the single instance of the class
      $db1 =    
             DatabaseConnection::getInstance();

      // Retrieve the PDO connection from the   
      // singleton
      $connection = $db1->getConnection();

       // Perform a database query
      $query = $connection->query(
               "SELECT * FROM users"
       );

      // Fetch the results
      $results = $query->fetchAll(
                        PDO::FETCH_ASSOC
             );

      print_r($results);


      Key Points:
      —————
   -Private constructor: Ensures no external 
     code can directly create an instance of 
     the class. Only the class itself can create 
     an instance.
   -Static getInstance() method: This is the 
     only way to get the single instance of the 
     class. It checks if the instance already 
     exists; if not, it creates one.
   -Database connection: The __construct() 
     method handles the connection using 
     PDO (PHP Data Objects), which is a 
     modern way of connecting to databases 
     in PHP. Placeholder credentials 
     (localhost, root, password) are used here 
     for demonstration.
   -When you call the getInstance() static 
     method of DatabaseConnection, it 
     checks if an instance of the class already 
     exists. If it doesn’t, the class creates an 
     instance and establishes the database 
     connection.
   -All subsequent calls to getInstance() will 
     return the same instance with the same 
     connection.
   -This ensures that only one database 
     connection is used throughout the 
     application, which can improve 
     performance and prevent multiple 
     connections from being opened 
     unnecessarily.

    Practical Use
    ——————
    This pattern is commonly used in 
  situations like managing a single database 
  connection in a web application, where 
  multiple database connections could lead 
  to inefficiency and resource exhaustion.




       FACTORY PATTERN
       ——————————-
    The factory pattern provides a way to 
  create objects without specifying the exact 
  class. It delegates the object creation 
  process to subclasses or another object.

    PHP example:
    —————

         interface Animal {
             public function speak();
         }

        class Dog implements Animal {
             public function speak() {
                 return 'Woof';
            }
         }

         class Cat implements Animal {
              public function speak() {
                   return 'Meow';
              }
         }

        class AnimalFactory {
               public static function  
                      createAnimal($type) {
                           if ($type == 'dog') {
                               return new Dog();
                           } elseif ($type == 'cat') {
                               return new Cat();
                           }
              }
        }


        Using the factory class
        ——————————
       $animal =    
              AnimalFactory::createAnimal('dog');
       echo $animal->speak();



    ABSTRACT FACTORY PATTERN
    ——————————————— 
    The Abstract Factory Pattern provides an 
  interface for creating families of related or 
  dependent objects without specifying their 
  concrete classes. It allows the client to 
  create objects that are part of a specific 
  family, where the exact class of each 
  object is determined by the factory.

    Example Scenario:  
    ——————-
    Let's say you’re building an application 
  that can work with two types of user 
  interfaces: Windows and Mac. Each UI has 
  its own specific elements (like buttons, 
  checkboxes), and the abstract factory will 
  help you create the appropriate family of 
  UI components based on the environment.

 
    PHP Code Example
    ———————
         // Abstract Factory interface
         interface GUIFactory {
              public function createButton();
              public function createCheckbox();
         }

        // Concrete Factory for Windows
       class WindowsFactory 
                          implements GUIFactory {
             public function createButton() {
                     return new WindowsButton();
             }

             public function createCheckbox() {
                     return new WindowsCheckbox();
             }
        }

       // Concrete Factory for Mac
       class MacFactory 
                           implements GUIFactory {
              public function createButton() {
                     return new MacButton();
              }
  
             public function createCheckbox() {
                     return new MacCheckbox();
             }
        }

        // Abstract Product for Button
        interface Button {
               public function render();
        }

       // Concrete Button for Windows
       class WindowsButton 
                            implements Button {
               public function render() {
                     echo "Rendering Windows 
                         Button\n";
               }
         }


        // Concrete Button for Mac
        class MacButton implements Button {
                 public function render() {
                     echo "Rendering Mac Button\n";
                 }
         }

        // Abstract Product for Checkbox
        interface Checkbox {
               public function check();
        }

        // Concrete Checkbox for Windows
        class WindowsCheckbox 
                             implements Checkbox {
               public function check() {
                      echo "Checking Windows 
                             Checkbox\n";
               }
          }

         // Concrete Checkbox for Mac
         class MacCheckbox 
                             implements Checkbox {
                  public function check() {
                        echo "Checking Mac 
                                Checkbox\n";
                   }
           }


          // Client code
          function renderUI(
                              GUIFactory $factory) {
               $button 
                        = $factory->createButton();
  
               $checkbox 
                       = $factory->createCheckbox();

  
               // Render button and checkbox
               $button->render();
               $checkbox->check();
          }

         // Example of using the abstract     
         // factory to render Windows UI
         $windowsFactory 
                                 = new WindowsFactory();
         renderUI($windowsFactory);

         // Example of using the abstract    
         // factory to render Mac UI
         $macFactory = new MacFactory();
         renderUI($macFactory);

         Key points
         —————-
   -Abstract Factory pattern allows 
       creating families of related objects.
   -You can easily switch between different 
       families (e.g., Windows vs Mac).



    BUILDER PATTERN
    ——————————
    The Builder Pattern is used to create 
  complex objects step by step. It separates 
  the construction of a complex object from 
  its representation, allowing the same 
  construction process to create different 
  representations.

  Example Scenario:
  ————————
    Consider building a ‘House’ with various 
  customisable features like walls, doors, 
  windows, and roof. Each house may differ 
  in terms of these features, but the 
  construction process is the same.


  PHP Code Example:
  ———————-
             // Product: House
             class House {
                    public $walls;
                    public $doors;
                    public $windows;
                    public $roof;

                    public function show() {
                        echo "House with 
                             {$this->walls} walls, 
                             {$this->doors} doors,    
                             {$this->windows} windows, 
                             and a {$this->roof} roof\n";
                    }
               }

              // Builder Interface
              interface HouseBuilder {
                    public function buildWalls();
                    public function buildDoors();
                    public function buildWindows();
                    public function buildRoof();
                    public function getHouse();
              }

             // Concrete Builder: Wooden House
             class WoodenHouseBuilder    
                           implements HouseBuilder {
                   private $house;

                   public function __construct() {
                         $this->house = new House();
                   }

                   public function buildWalls() {
                           $this->house->walls 
                                        = "Wooden";
                   }

                  public function buildDoors() {
                           $this->house->doors 
                                      = "Wooden";
                  }

                  public function buildWindows() {
                           $this->house->windows 
                                      = "Wooden";
                  }

                  public function buildRoof() {
                           $this->house->roof 
                                    = "Wooden";
                  }

                  public function getHouse() {
                          return $this->house;
                  }
             }

            // Director
           class ConstructionEngineer {
                  private $houseBuilder;

                  public function __construct(
                        HouseBuilder $houseBuilder) {
                             $this->houseBuilder 
                                       = $houseBuilder;
                  }

                 public function constructHouse() {
                          $this->houseBuilder
                                 ->buildWalls();
                          $this->houseBuilder
                                 ->buildDoors();
                          $this->houseBuilder
                                  ->buildWindows();
                          $this->houseBuilder
                                  ->buildRoof();
    
                         return $this->houseBuilder
                              ->getHouse();
                   }
              }

             // Client code
             $builder = 
                     new WoodenHouseBuilder();
             $engineer = new     
                     ConstructionEngineer($builder);

             $house = 
                  $engineer->constructHouse();
             $house->show();

      Key points:
      ——————
   -The Builder Pattern focuses on step-by-
     step construction of complex objects.
   -The same building process can create 
     different results (e.g., WoodenHouse or 
     BrickHouse).



    PROTOTYPE PATTERN
    ———————————-
    The Prototype Pattern involves creating 
  new objects by copying an existing object 
  (the prototype). This is useful when the 
  cost of creating a new object is expensive, 
  and you can avoid it by cloning an existing 
  object.

  Example Scenario:  
  ———————-
    Imagine you are creating a ‘Document 
  Editor’ where you need to create copies of 
  documents. The prototype pattern allows 
  you to create new documents by cloning 
  existing ones instead of creating them from 
  scratch.


    PHP Code Example:
    —————————-
              // Prototype interface
              interface DocumentPrototype {
                     public function clone();
               }

             // Concrete Prototype: TextDocument
            class TextDocument 
                   implements DocumentPrototype {
  
                  private $content;

                  public function __construct(
                           $content) 
                  {
                        $this->content = $content;
                   }

                  // Clone the document
                  public function clone() {
                     return new TextDocument(
                              $this->content);
                   }

                 // Display document content
                 public function showContent() {
                     echo "Document content: " . 
                          $this->content . "\n";
                 }
              }

             // Client code
             $originalDocument = 
                       new TextDocument(
                               "Original Content");
             $clonedDocument =    
                       $originalDocument->clone();

            // Display the contents of both the 
            // original and the cloned document
            $originalDocument->showContent();
            $clonedDocument->showContent();


    Key points:
    —————-
   -Prototype Pattern allows creating new 
     objects by copying an existing object.
   -It is useful for cases where object 
     creation is expensive, and copying is 
     more efficient.



    2) Structural Design Patterns
    ———————————————
    Structural design patterns focus on how 
  objects and classes are composed   
  (structured). They help ensure that if one 
  part of a system changes, the entire 
  structure doesn’t need to change. These 
  structural patterns help organize and 
  manage the relationships between classes 
  and objects in your applications, providing 
  flexibility, reusability, and better 
  organization. The following design patterns 
  fall under this group:

    -ADAPTER PATTERN
    -DECORATOR PATTERN
    -FACADE PATTERN
    -COMPOSITE PATTERN
    -PROXY PATTERN



    ADAPTER PATTERN
    ——————————-
    The Adapter Pattern allows objects with 
  incompatible interfaces to work together. It 
  acts as a bridge between two interfaces 
  that otherwise couldn’t interact directly. 
  This is useful when you want to integrate a 
  class with a different interface into your 
  system.

    Example Scenario:
    ——————-
    Imagine you have a Media Player that 
  only supports playing MP3 files, but you 
  want to add support for playing MP4 files. 
  The adapter pattern helps convert the MP4 
  interface to be compatible with the MP3 
  player.

    PHP code example
   ——————————
            // Target interface 
            // (the one we want to use)
            interface MediaPlayer {
                  public function play($filename);
            }

           // To-be-adapted class (class with   
           // incompatible interface)
          // lets refer to it as the ‘source’ class
          class MP4Player {
               public function playMP4($filename)    
              {
                   echo "Playing MP4 file: " . 
                         $filename . "\n";
              }
          }

         // Adapter class that converts the 
         // source’s interface to the Target 
         // interface
         class MediaAdapter 
                     implements MediaPlayer {
  
               private $mp4Player;

               public function __construct(
                     MP4Player $mp4Player) {
                           $this->mp4Player 
                                        = $mp4Player;
               }

              // Implement the play method of    
             // MediaPlayer by using MP4Player's  
             // method
             public function play($filename) {
                   $this->mp4Player
                         ->playMP4($filename);
             }
        }

        // Client code
       function playMedia(
             MediaPlayer $player, $filename) 
       {
             $player->play($filename);
        }

       // Example usage
       $mp4Player = new MP4Player();
       $adapter = 
                   new MediaAdapter($mp4Player);

       // Use the adapter to play an MP4 file 
       // with a MediaPlayer interface
       playMedia($adapter, "example.mp4");

       Key points
       —————-
   -The Adapter Pattern enables objects with 
     incompatible interfaces to work together.
   -You can introduce new functionality to an 
     existing system without changing its 
     structure.



      DECORATOR PATTERN
      ————————————
    The decorator pattern allows behavior to 
  be added to individual objects, either 
  statically or dynamically, without affecting 
  the behavior of other objects from the 
  same class.

    PHP example:
    ———————-
          // Decorator pattern in PHP
          // Coffee interface (Component)
          interface Coffee {
               public function getCost();
               public function getDescription();
          }

         // SimpleCoffee class (Concrete        
         // Component)
        class SimpleCoffee implements Coffee {
                public function getCost() {
                    return 10;
                }

               public function getDescription() {
                     return "Simple Coffee";
               }
         }

         // Decorator base class (Optional, used     
         // for type hinting)
         abstract class CoffeeDecorator 
                                         implements Coffee {
                   protected $coffee;

                  public function __construct(
                                              Coffee $coffee) {
                             $this->coffee = $coffee;
                   }
           }

           // MilkDecorator class (Concrete 
           // Decorator)
           class MilkDecorator extends     
                                       CoffeeDecorator {
                   public function getCost() {
                         // Add the cost of milk
                        return $this->coffee
                              ->getCost() + 2;
                   }

                 public function getDescription() {
                         // Add milk to the description
                        return $this->coffee
                            ->getDescription() . ", Milk";
                 }
             }

             // SugarDecorator class (Concrete    
             // Decorator)
            class SugarDecorator extends 
                                              CoffeeDecorator {
                public function getCost() {
                    // Add the cost of sugar
                    return $this->coffee
                                    ->getCost() + 1;
               }

              public function getDescription() {
                     // Add sugar to the description
                     return $this->coffee
                         ->getDescription() . ", Sugar";
               }
          }

         // Client code
         $coffee = new SimpleCoffee();

         // Add milk to the coffee
         $coffeeWithMilk = new      
         MilkDecorator($coffee);

         // Add sugar to the coffee with milk
         $coffeeWithMilkAndSugar = new    
         SugarDecorator($coffeeWithMilk);

         // Output: 13 (10 + 2 for milk + 1 for 
         // sugar)
         echo $coffeeWithMilkAndSugar
                          ->getCost();
         echo "\n";

         // Output: Simple Coffee, Milk, Sugar
         echo $coffeeWithMilkAndSugar
                        ->getDescription();


        Key points
        —————-
   -The purpose of the Decorator Pattern is 
     to allow behavior to be added to 
     individual objects dynamically, without 
     affecting the behavior of other objects 
     from the same class. This is done by 
     “wrapping” the object with decorator 
     classes that enhance or modify its 
     functionality.
   -The pattern is called a decorator 
     because the decorator class is used to 
     “decorate” or wrap the original class, 
     adding additional features or 
     responsibilities to the object being 
     decorated. Each decorator class 
     implements the same interface or inherits 
     from the same parent class as the 
     original object.
   -Here is how the Code really Works:
	Component Interface (Coffee):
          -This Coffee interface defines the core 
             functionality, which in this case is 
             getCost() and getDescription(). 
           -This sets the contract for both the 
             base class (SimpleCoffee) and the 
             decorators.
	Concrete Component (SimpleCoffee):
           -This class implements the basic 
             behavior of the Coffee interface. In 
             this case, it represents a simple 
             coffee with a base cost and 
             description.
	Decorator Classes (MilkDecorator and 
         SugarDecorator):
           -These decorator classes implement 
             the same Coffee interface but 
             enhance the functionality of the 
             base class (SimpleCoffee). They add 
             their own behavior-in this case 
             flavour (adding milk or sugar) while 
             still calling the base class’s methods 
             to maintain the existing functionality.
	       For example, MilkDecorator adds 
             the cost of milk to the base coffee 
             and modifies the description to 
             include milk. Similarly, 
             SugarDecorator adds the cost of 
             sugar and updates the description.
   -The decorator pattern also allows for 
     dynamic and flexible behavior addition. 
     You can apply multiple decorators in 
     sequence, as shown in the example 
     where we first decorate the coffee with 
     milk and then with sugar. Each decorator 
     adds to the cost and description, building 
     on top of the previous one.
	
    Use Case:
    ——————
    The Decorator Pattern is useful when you 
  want to add functionality to an object 
  without modifying its code, especially 
  when you need to apply different 
  combinations of behavior (e.g., coffee with 
  milk, coffee with sugar, coffee with milk 
  and sugar, etc.). Instead of creating 
  multiple subclasses to represent each 
  combination, you can achieve this through 
  decorators.
	This decorator pattern provides 
  flexibility because you can “stack” multiple 
  decorators in any order and combine them 
  as needed without altering the underlying 
  object.


  

      FACADE PATTERN
      —————————-
    The facade pattern provides a simplified 
  interface to a complex subsystem. It hides 
  the complexities of the system behind a 
  unified, easy-to-understand interface.

    PHP example:
    ———————
             // Facade pattern in PHP
            class CPU {
                   public function freeze() {
                         echo "CPU is frozen\n";
                   }
  
                  public function jump($position) {
                        echo "CPU jumps 
                                to $position\n";
                   }
  
                  public function execute() {
                        echo "CPU is executing 
                                instructions\n";
                  }
              }

              class Memory {
                       public function load(
                                      $position, $data) {
                              echo "Loading $data into    
                                     position $position\n";
                        }
               }

              class HardDrive {
                      public function read(
                                       $sector, $size) 
                      {
                             echo "Reading $size bytes         
                                  from sector $sector\n";
                       }
                }

                class ComputerFacade {
                        private $cpu;
                        private $memory;
                        private $hardDrive;

                       public function __construct() {
                            $this->cpu = new CPU();
                            $this->memory = 
                                                new Memory();
                             $this->hardDrive = 
                                             new HardDrive();
                       }

                       public function start() {
                            $this->cpu->freeze();
                            $this->memory
                                   ->load(0x1000, "OS");
                            $this->hardDrive
                                   ->read(0x1000, 512);
                            $this->cpu->jump(0x1000);
                            $this->cpu->execute();
                       }
                  }

                 // Usage
                 $computer = 
                          new ComputerFacade();
                          $computer->start();


      Key points:
      ——————
   -The Facade Pattern provides a simplified 
     interface to a complex system or a set of 
     classes. It hides the complexity of the 
     subsystems and offers a unified, easier-
     to-use interface for the client.
   -It is called a Facade because it acts as 
     the “front” or “face” of a set of 
     subsystems. Instead of interacting 
     directly with complex subsystems (like 
     CPU, Memory, and HardDrive in this 
     example), the client interacts with a 
     single class (the ComputerFacade) that 
     manages the communication with the 
     subsystems behind the scenes.
   -Here is how the Code Works:
	Subsystem Classes:
	   -The CPU, Memory, and HardDrive are 
            the individual subsystems that 
            perform specific tasks.
	  -Each class has its own methods 
           (freeze, jump, load, etc.) that are 
           specific to its responsibility.
	Facade Class (ComputerFacade):
	   -This class aggregates (beings 
           together) the subsystems (CPU, 
           Memory, and HardDrive) and exposes 
           a simple start() method to the client.
	  -The start() method internally 
           coordinates the necessary calls to the 
           subsystems, such as freezing the 
           CPU, loading data into memory, 
           reading from the hard drive, and 
           finally executing instructions.
	  -The client only needs to call start(), 
          without worrying about the individual 
          steps required to boot the computer.
	

    Use Cases:
    ——————
   -This can be used anywhere to simplify   
     complex systems. When you have a 
     system composed of several intricate 
     components (such as hardware or APIs), 
     a facade can simplify interactions by 
     consolidating them into a single, 
     easy-to-use interface. For example, in 
     this case, starting a computer requires 
     multiple steps, but the facade hides all 
     that complexity.
   -It can be used to Reducing Tight 
     Coupling. The client code is only coupled 
     with the facade (ComputerFacade), not 
     with the individual subsystems (CPU, 
     Memory, HardDrive). This makes the 
     code easier to maintain and modify.
   -It can also be used to Improve Code 
     Readability. Facade patterns are often 
     used to improve code readability. If the 
     client had to call each subsystem's 
     methods directly, the code would be 
     more complex and harder to read.
   -Another great benefit of the facade 
     pattern allows for a clean, organised 
     separation between the client and the 
     complex internals of a system. It also 
     allows for easier maintenance because 
     changes to the subsystem do not affect 
     the client, as long as the facade's 
     interface remains consistent.
   -You can see how the Facade Pattern 
     simplifies interactions with a complex 
     system by creating a single entry point 
     for the client to use.


      COMPOSITE PATTERN
      ————————————
    The Composite Pattern is used to treat 
  individual objects and compositions of 
  objects uniformly. This pattern allows you 
  to build a tree structure where individual 
  objects and groups of objects are handled 
  the same way.

  Example Scenario:
  —————————
    A company might have employees who 
  can be regular staff members or managers 
  who supervise other employees. The 
  composite pattern helps manage this 
  hierarchy by treating both employees and 
  managers uniformly.

    PHP Example:
    ————-
            // Component interface
            interface Employee {
                 public function getDetails();
            }

           // Leaf class: Represents individual       
           // employees
           class Staff implements Employee {
                  private $name;
                  private $position;

                  public function __construct(
                        $name, $position) 
                  {
                         $this->name = $name;
                         $this->position = $position;
                   }

                  public function getDetails() {
                     echo "{$this->name} is a {
                           $this->position}\n";
                  }
             }

            // Composite class: Represents 
            // managers that can have    
            // subordinates
          class Manager implements Employee {
                    private $name;
                    private $position;
                    private $subordinates = [];

                public function __construct(
                                           $name, $position) {
                    $this->name = $name;
                    $this->position = $position;
                }

                // Add a subordinate (can be either     
              //staff member or another manager)
                public function add(
                                    Employee $employee) {
                       $this->subordinates[] 
                                 = $employee;
                 }

                public function getDetails() {
                         echo "{$this->name} is a    
                         {$this->position} and has the    
                         following subordinates:\n";
                      foreach (
                            $this->subordinates 
                                             as $employee) {
                         $employee->getDetails();
                      }
                  }
              }

             // Client code
             $manager = 
                        new Manager("Alice", "CEO");
             $staff1 = 
                       new Staff("Bob", "Developer");
             $staff2 = 
                    new Staff("Charlie", "Designer");

             $manager->add($staff1);
             $manager->add($staff2);

             // Display details of the manager and     
             // their subordinates
            $manager->getDetails();


    Key points
    —————-
   -The Composite Pattern allows you to 
     treat individual objects and groups of 
     objects the same way.
   -It is useful for representing hierarchical 
     structures like employees, files, or GUI 
     components.



      PROXY PATTERN
      —————————
    The Proxy Pattern provides a placeholder 
  or surrogate for another object to control 
  access to it. It's used to add an extra level 
  of control before accessing the actual 
  object, such as in cases of lazy loading, 
  access control, or logging.

    Example Scenario:
    ————————
    Imagine you have a large image that 
  takes time to load. Instead of loading it 
  directly, you can use a proxy that loads the 
  image only when it's needed.

    PHP Code Example:
    —————————-
            // Subject interface
            interface Image {
                  public function display();
           }

           // RealSubject: The actual large image  
           // class
          class RealImage implements Image {
                   private $filename;

                   public function __construct(
                                                   $filename) {
                         $this->filename = $filename;
                         $this->loadFromDisk();
                    }

                   // Simulate loading the image 
                   // from disk
                   private function loadFromDisk() {
                        echo "Loading image: " .     
                               $this->filename . "\n";
                    }

                   public function display() {
                         echo "Displaying image: " .     
                         $this->filename . "\n";
                  }
          }

          // Proxy class: Controls access to 
          // RealImage
         class ProxyImage implements Image       
         {
                 private $realImage;
                 private $filename;

                 public function __construct(
                                                     $filename) {
                         $this->filename = $filename;
                 }

               // Display the image, loading it     
               // only if necessary
               public function display() {
                    if ($this->realImage === null) {
                             // Only load the image if it's   
                             // not already loaded
                            $this->realImage = 
                                 new RealImage(
                                          $this->filename);
                    }
    
                    $this->realImage->display();
                }
         }

        // Client code
        $image = 
              new ProxyImage("large_photo.jpg");

        // Image is not loaded yet; only the 
        // proxy is created
       echo "Image proxy created but not 
       loaded yet.\n";

       // Now we display the image, which 
      // triggers loading and displaying
      $image->display();


     Key points:
     ——————
   -The Proxy Pattern provides a surrogate to 
     control access to another object.
   -It’s useful for lazy initialisation, access 
     control, or logging.



    3) Behavioral Design Patterns
    ———————————————-
    Behavioral design patterns focus on how 
  objects communicate and interact with 
  each other. They define the way in which 
  classes and objects collaborate. The   
  following design patterns fall under this 
  group:

    -OBSERVER PATTERN
    -STRATEGY PATTERN
    -TEMPLATE METHOD PATTERN
    -COMMAND PATTERN
    -ITERATOR PATTERN




      OBSERVER PATTERN
      ———————————
    The observer pattern is used when there 
  is one subject and multiple observers that   
  depend on the subject's state. Whenever 
  the subject changes its state, it notifies all 
  its observers.

   PHP example:
   ———————
          // Observer pattern in PHP
         class Subject {
                private $observers = [];

                public function addObserver(
                      $observer) 
                {
                    $this->observers[] = $observer;
                }

                public function notify() {
                     foreach ($this->observers as     
                                                        $observer) {
                         $observer->update();
                     }
                }
          }

         class Observer {
                public function update() {
                     echo 'Observer notified';
                }
         }

        // Usage
        $subject = new Subject();
        $observer = new Observer();
        $subject->addObserver($observer);
        $subject->notify();




        STRATEGY PATTERN
        ———————————
    The strategy pattern allows you to define 
  a family of algorithms, encapsulate each 
  one, and make them interchangeable. It 
  lets the algorithm vary independently from 
  the clients that use it. In other words, the 
  clients use the algorithm relevant to them 
  through the family interface.
    This pattern needs 
      -an interface to define the algorithm 
       family
     -one or more strategy classes to 
       implement the different algorithms in 
       the family, each in its own way
     -one client-interfacing class (often 
       referred to as context) to bring the 
       two (interface and strategies) together, 
       which clients will use to vary their 
       strategies seamlessly.

   PHP example:
   ———————-
            // Strategy pattern in PHP
            interface PaymentStrategy {
                 public function pay($amount);
            }

            // Concrete strategy class: PayPal
            class PayPalStrategy implements 
                                             PaymentStrategy {
                  public function pay($amount) {
                          return "Paid $amount using    
                                 PayPal";
                  }
            }

           class CreditCardStrategy implements      
                                            PaymentStrategy {
                 public function pay($amount) {
                          return "Paid $amount using    
                                Credit Card";
                 }
           }

          // Context class that will use the 
         // PaymentgStrategy interface to select 
         // the relevant strategy each time
          class PaymentContext {
                private $strategy;

                // Inject the chosen strategy into 
                // the context
                public function __construct(
                         PaymentgStrategy $strategy)     
                {
                      $this->strategy = $strategy;

                // Execute the chosen strategy's 
                // payment method
               public function     
                           executePayment($amount) {
                    return $this->strategy
                                             ->pay($amount);
                }
           }

           // Client code

           // Choose PayPal as the payment 
           // strategy
           $context = new PaymentContext(
                              new PayPalStrategy()
                         );

           // Output: Paid 100 using PayPal
         echo $context->executePayment(100);
         echo "\n";

         // Switch payment strategy to credit 
         // card
         $context = new PaymentContext(
                         new CreditCardStrategy());

          // Output: Paid 200 using Credit Card
        echo $context->executePayment(200); 


      Key points and explanation 
      ————————————
   -The purpose of the Strategy Pattern is to 
     define a family of algorithms (or 
     strategies) that can be used 
     interchangeably. Instead of hardcoding 
     specific behaviour into a class, different 
     strategies are encapsulated in separate 
     classes, allowing the behaviour to be 
     selected at runtime.
   -Here is how it works:
      -Strategy Interface (PaymentStrategy)
        This interface defines the common 
        method pay($amount) that all concrete 
        strategies must implement. It ensures 
        that all payment methods share the 
        same contract.

     -Concrete Strategies (PayPalStrategy, 
       CreditCardStrategy):
       These classes implement the 
       PaymentStrategy interface and provide 
       the specific behaviour for how the 
       payment is processed. For example, 
       PayPalStrategy handles payments using 
       PayPal, while CreditCardStrategy 
       handles payments using a credit card.

     -Context Class (PaymentContext):
       The context class (PaymentContext) is 
       responsible for interacting with the 
       chosen strategy. It accepts a 
       PaymentStrategy object as a parameter 
       and uses it to process the payment 
       without knowing the details of how the 
       payment is handled. This decouples the 
       client code from the specific strategies.
	

      Use Cases:
      ——————-
   -Dynamic Behaviour Selection. When you 
     need to switch between different 
     algorithms or behaviours at runtime, the 
     strategy pattern is useful. For example, 
     choosing different payment methods (like 
     PayPal or credit card) based on the 
     user’s preference.
   -Avoiding Conditional Logic. Instead of 
     using complex if-else or switch 
     statements to determine the behaviour, 
     the strategy pattern encapsulates these 
     behaviours into separate classes, making 
     the code more maintainable and flexible.
   -The big benefit of the Strategy Pattern is
     that it promotes the open/closed 
     principle (the O in the SOLID principles). 
     It is open for extension in that new 
     strategies can be added without 
     changing the existing code in the 
     PaymentContext. The pattern allows for 
     flexibility by letting you swap out 
     behaviour dynamically while keeping the 
     code structure clean and modular.




        TEMPLATE METHOD PATTERN
        ———————————————-
    The Template Method Pattern defines the 
  skeleton of an algorithm in a base class, 
  while allowing subclasses to override 
  specific steps of the algorithm without 
  changing its structure. It is called the 
  Template Method because it provides a 
  template for the overall process, with some 
  steps left open for customisation by 
  subclasses.

   PHP Example:
   ———————-
             // Abstract class with the template 
            // method
            abstract class MealPreparation {
      
                 // Template method
                public function prepareMeal() {
                     $this->boilWater();
                     $this->cook();
                     $this->serve();
                 }

               // Common step
               public function boilWater() {
                   echo "Boiling water\n";
               }

              // Steps to be implemented by 
              // subclasses
              abstract public function cook();

             // Common step
             public function serve() {
                   echo "Serving the meal\n";
             }
        }

       // Concrete class: preparing pasta
       class PastaMeal extends 
                                      MealPreparation {
             public function cook() {
                   echo "Cooking pasta\n";
             }
       }

      // Concrete class: preparing rice
    class RiceMeal extends MealPreparation {
            public function cook() {
                    echo "Cooking rice\n";
           }
    }

   // Client code
   $pastaMeal = new PastaMeal();
   $pastaMeal->prepareMeal();
   // Output: Boiling water, Cooking pasta,    
   Serving the meal

   echo "\n";

   $riceMeal = new RiceMeal();

   // Output: Boiling water, Cooking rice,  
   Serving the meal
   $riceMeal->prepareMeal();


    Key points
    —————-
   -The purpose of the template design 
     pattern is that it allows you define the 
     framework of an algorithm in a base 
     class, leaving the details of specific steps
     to be implemented by subclasses.
   -Here is How It Works:

	Template Method (prepareMeal):
        This method is defined in the base 
         class (MealPreparation) where the 
         algorithm’s structure is defined. Here,  
         some steps (like boilWater and serve) 
         are common, while others (like cook) 
         are left abstract for subclasses to 
         implement.

     There are concrete Classes (PastaMeal, 
     RiceMeal):
     These classes implement the step (cook) 
     in their own way, allowing flexibility while 
     still following the overall process defined 
     by the base class.
	

      Use Case:
      ——————
    The Template Method Pattern is useful 
  when multiple classes share a similar 
  process but require customisation for 
  specific steps. In the example, both pasta 
  and rice meals follow the same process but 
  differ in the cooking step.




        COMMAND PATTERN
        ———————————-
    The Command Pattern turns a request 
  into an object, allowing the 
  parameterisation of clients with queues, 
  requests, or logs. It is called Command 
  because each object represents an 
  operation to be executed, stored, or 
  undone.

    PHP Example:
    ———————-
 <?php

// Command interface
interface Command {
  public function execute();
}

// Concrete command: Turn on the light
class LightOnCommand implements Command {
  private $light;

  public function __construct($light) {
    $this->light = $light;
  }

  public function execute() {
    $this->light->turnOn();
  }
}

// Concrete command: Turn off the light
class LightOffCommand implements Command {
  private $light;

  public function __construct($light) {
    $this->light = $light;
  }

  public function execute() {
    $this->light->turnOff();
  }
}

// Receiver class: Light
// called receiver coz its a class that actually
// receives & executes the command sent by 
// the command classes behind the scenes
class Light {
  public function turnOn() {
    echo "Light is ON\n";
  }

  public function turnOff() {
    echo "Light is OFF\n";
  }
}

// Invoker class: Remote control
// Called the invoker coz from the client side, 
// it sets the ball rolling towards getting the 
// command to do its job. It does that in its 
// PressButton() method.
class RemoteControl {
  private $command;

  public function setCommand(
               Command $command) 
  {
          $this->command = $command;
  }

  public function pressButton() {
    $this->command->execute();
  }
}

// Client code
———————
 // prepare the receiver of the command 
$light = new Light();

$remote = new RemoteControl();

// Set the required command interface 
// (light on or light off)
// Light on
$remote->setCommand(
                   new LightOnCommand($light)
     );

 // Output: Light is ON
$remote->pressButton(); 

// Light off
$remote->setCommand(
                   new LightOffCommand($light)
    );

 // Output: Light is OFF
$remote->pressButton(); 


       Key points
       —————-
   Purpose of the Command Pattern:
This pattern encapsulates requests as objects, allowing you to parameterise methods, delay execution, and queue operations. It decouples the invoker (client) from the object that performs the actual work (receiver).
	
   How It Works:
   ———————
  Command Interface:
    -The Command interface defines a 
      method (execute()) that will be 
      implemented by different commands.
  Concrete Commands 
    -LightOnCommand, 
    -LightOffCommand):

       These command classes accept a 
        receiver class Light. This makes sense 
        because their command action is all 
        about light. 
       Through their execute() methods, these 
        classes indirectly implement the 
        specific actions (turnOn, turnOff) by 
        delegating the work to the Light 
        receiver class which is the class having 
        these turnOn() and turnOff() methods. 
        Which of them is called will depend on 
        the command interface-so it will be 
        turnOn() or turnOff() for 
        LightOnCommand and 
        LightOffCommand respectively.

  Invoker (RemoteControl):
      The invoker class stores a command and 
    executes it when the client presses a 
    button. The invoker doesn't know the 
    details of what the command does, it 
    simply executes the execute() method on 
    the command.
      It is called the invoker because the 
    execution of the command starts from it. 
    It all starts from its PressButton() method. 
    It then runs the execute() method on the 
    command which it had already stored in 
    its '$command' property.
	
  Use Case:
  —————-
    The Command Pattern is useful for 
  implementing undo/redo functionality, 
  executing commands in sequence, or 
  logging operations for future execution. In 
  the example, a remote control can switch 
  between different commands (turning the 
  light on or off) without knowing how each 
  command works internally.



        ITERATOR PATTERN
        ——————————-
    The Iterator Pattern provides a way to 
  access the elements of a collection (like an 
  array or list) sequentially without exposing 
  the underlying structure. It is called Iterator 
  because it “iterates” over a collection one 
  element at a time.

      PHP Example:
     ———————-
<?php
// Iterator pattern in PHP

// Collection interface
interface Collection {
  public function getIterator();
}

// Concrete collection class
class BookCollection implements Collection {
  private $books = [];

  public function addBook($book) {
    $this->books[] = $book;
  }

  public function getIterator() {
    return new BookIterator($this->books);
  }
}

// Iterator class
class BookIterator {
  private $books;
  private $index = 0;

  public function __construct($books) {
    $this->books = $books;
  }

  public function hasNext() {
    return $this->index 
            < count($this->books);
  }

  public function next() {
    return $this->books[$this->index++];
  }
}

// Client code
$bookCollection = new BookCollection();
$bookCollection->addBook("Design Patterns");
$bookCollection->addBook("Clean Code");

$iterator = $bookCollection->getIterator();
while ($iterator->hasNext()) {
  echo $iterator->next() . "\n";  
}
// Output:
// Design Patterns
// Clean Code


     Key points
    -—————-
    Purpose of the Iterator Pattern:
  This pattern allows clients to traverse 
  through the elements of a collection 
  without needing to know the underlying 
  structure of the collection. It provides a 
  standardised way to access and iterate 
  over data.
	
    How It Works:
    ———————
  Collection Interface (Collection):
     -This defines a method (getIterator()) to 
       return an iterator for the collection. 
     -Concrete collection classes (like 
       BookCollection) implement this method.
  Iterator Class (BookIterator):
    -The BookIterator class is a class that 
      stands on its own and is used by the 
      BookCollection classes (via their 
      getIterator() methods to which they will 
      pass their array of items-be it books or 
      anything else). It defines methods 
      like hasNext() and next() to access 
      elements in the collection one by one.

    Client Code:
    ——————-
    The client doesn’t need to know how the 
  BookCollection stores its books. It just uses 
  the iterator to access the books 
  sequentially using hasNext() and next().
	
    Use Case:
    ——————
    The Iterator Pattern is useful when you 
  need to traverse a collection without 
  exposing its internal details. It is especially 
  helpful when working with custom data 
  structures or complex collections. In the 
  example, the BookIterator provides a 
  simple way to loop through a collection of 
  books without directly accessing the array.






——————————————————————
	DATABASES AND STORAGE
——————————————————————

                      -Models
                        -The $_hasParent and $_hasChild Properties
                        -What these properties mean
                        -The array format
                        -OMITTING THE FOREIGN KEY (CONVENTION OVER CONFIGURATION)
                        -EXAMPLE — THE PRODUCTS MODEL
                        -Lazy loading related data
                      -The Dorguzen Migration System
                        -Running Migrations (CLI Commands)
                      -Database Seeding
                        -The Seeding Architecture
                        -CLI Commands
                        -Seeder Classes
                        -Factories — Automatic Record Generation
                        -Data Pools — Smart Fake Data
                        -Factory Lifecycle Hooks
                        -Unique Values
                        -Environmant Protection
                        -How everything works together
                        -When Should You Use Seeding?
                      -Dorguzen's support for multiple DB drivers
                        -Architecture Overview
                        -How database access works 
                        -Database Driver API Reference
                      -Neo4j Graph Database Support in Dorguzen



-This section covers everything about database access in terms of SQL queries, database configuration, and database 
  design and management.

-Learn the SQL (Structured Query Language) which is a global standard for communication with most database systems.
-What types of database systems Dorguzen supports, for example, RDBS (Relational database systems) like 
MySQL, PosgreSQL, etc, and if any noSQL database system like Neo4j is supported
-It also covers what APIs Dorguzen provides to use in connecting with your chosen database solution.





      Models
      -------

  Dorguzen has an ORM system that follows the active record system where model classes are mapped to corresponding 
  database table names. Here are some conventions to follow:

    -The model class files live in src\models.

    -All model classes must extend the parent model, which is Dorguzen\Core\DGZ_Model.

    -The model class names should be identical with the database table names, with the only exception that the table 
      names begin in lowercase. For example, if a model name is Users, the database table name should be users. If the 
      model name is BaseSettings, the database table name should be baseSettings.

    -Ideally, every database table name should have a corresponding model class of exactly the same name, with the table 
      name beginning in lowercase. However, if the table name is different from its correspondent model class, you should 
      make Dorguzan aware of this by giving the model class a property named 'table', and assigning it the value of the 
      actual table name. For example if you have a Customers model, and its corresponding database table is named 'custos',
      instead of the conventional 'customers'; you must give the class a $table property like this:

          protected $table = 'custos';

    -Dorguzen expects you to give the primary key field of your tables the name 'id', or a name formed from the model 
     name with a suffix of '_id'; for example the primary key field of the users table can be 'id', or 'users_id'. 
      If you do not follow this convention, then you should make Dorguzen aware of the primary key field name of your 
      model's table by giving the model a property named 'id', and its value should be the table's primary key field.
      For example, do this in the Users model because the primary key field is not named to convention:

          protected $id = 'usersId';


      The $_hasParent and $_hasChild Properties
      -----------------------------------------
    -All model classes can optionally have two very important properties if you want to define the relationship between
      two models (or database tables). To do so, you should add the following two properties to your model; $_hasParent
      and $_hasChild, and they are arrays. These properties must be declared with the 'protected' keyword (not 'private')
      so that Dorguzen can read them internally.


          protected $_hasParent = [];

          protected $_hasChild = [];


      WHAT THESE PROPERTIES MEAN
      -----------------------------

      Think of it like a family tree. If you have a Users table and a Posts table, and every post belongs to a user, then:
        - From the Post's point of view, Users is its PARENT (a post belongs to a user).
        - From the User's point of view, Posts is its CHILD (a user can have many posts).

      $_hasChild tells Dorguzen: "These are the tables/models that have a foreign key pointing back to ME."
      $_hasParent tells Dorguzen: "These are the tables/models that I have a foreign key pointing TO."



      THE ARRAY FORMAT
      -----------------

      Each entry in both arrays follows this format:

          ModelClass::class => 'foreign_key_field_name'

      For $_hasChild — the key is the child model class, and the value is the name of the foreign key field
      on the CHILD's table that points back to this model:

          protected $_hasChild = [
              Posts::class    => 'post_author_id',   // the posts table has a field called 'post_author_id'
              Comments::class => 'comment_user_id',  // the comments table has a field called 'comment_user_id'
          ];

      For $_hasParent — the key is the parent model class, and the value is the name of the foreign key field
      on THIS model's table that points to the parent:

          protected $_hasParent = [
              Department::class => 'user_department_id',  // this table has a field called 'user_department_id'
          ];

      You may also use a plain string class name instead of the ::class constant. Both of the following are
      accepted by Dorguzen:

          Posts::class => 'post_author_id'    // recommended — safer, IDE-friendly
          'Posts'      => 'post_author_id'    // also works



      OMITTING THE FOREIGN KEY (CONVENTION OVER CONFIGURATION)
      ----------------------------------------------------------

      If you leave the foreign key value as an empty string '', Dorguzen will automatically work out what
      the foreign key field is called, based on a naming convention.

      For $_hasChild entries: Dorguzen assumes the child's foreign key field is named after the PARENT class,
      all in lowercase, with '_id' added at the end.

          Example: if the parent model is named 'Users', Dorguzen will look for a field called 'users_id'
          on the child's table.

      For $_hasParent entries: Dorguzen assumes this model's foreign key field is named after the PARENT class,
      all in lowercase, with '_id' added at the end.

          Example: if the parent model is named 'Department', Dorguzen will look for a field called
          'department_id' on this model's table.

      So this:
          Posts::class => ''

      ...is exactly the same as:
          Posts::class => 'users_id'

      ...if the current model is named 'Users'. Only omit the FK value if your database column actually follows
      this convention. If it does not, always specify the FK field name explicitly.



      EXAMPLE — THE PRODUCTS MODEL
      ------------------------------

          protected $_hasParent = [
              Location::class => 'location_id',         // products table has a 'location_id' FK pointing to locations
          ];

          protected $_hasChild = [
              Prods_to_categories::class => 'product_id',
              Product_images::class      => 'product_images_product_id',
              Product_videos::class      => 'product_videos_product_id',
          ];

      The above tells Dorguzen that the products table references the locations table via its 'location_id'
      field, and that three other tables each have their own foreign key field pointing back to the products table.


    -Here is the example format of a complete model class in Dorguzen:

        namespace Dorguzen\Models;

        use Dorguzen\Config\Config;
        use Dorguzen\Core\DGZ_Model as Model;

        class Users extends Model {
            // add this only if the matching table is not the class name beginning in lowercase
            protected $table = 'users';

            // all models must have these two properties
            protected $_columns = [];
            protected $data = [];

            // optional — declare your PK field name if it does not follow the DGZ convention
            protected $id = 'users_id';

            // optional — for managing entity relationships (must be 'protected', not 'private')
            protected $_hasParent = [
                Department::class => 'users_department_id',
            ];

            protected $_hasChild = [
                Posts::class    => 'post_author_id',
                Comments::class => 'comment_user_id',
            ];

            public function __construct(?Config $config)
            {
                return parent::__construct($config);
            }
        }


    CREATING A MODEL (CLI)
    ----------------------

      Use the make:model command to generate a new model stub:

          php dgz make:model Products

      This creates: src/models/Products.php

      You can also create the paired migration file at the same time by adding the -m option:

          php dgz make:model Products -m

      This creates both:
          src/models/Products.php
          database/migrations/YYYY_MM_DD_HHMMSS_create_products_table.php

      The migration name defaults to create_{model}_table (lowercased). If you want a custom
      migration name, pass it as the value of -m:

          php dgz make:model Products -m create_shop_products_table

      The generated migration is a skeleton — it contains a placeholder id(), string('name'), and
      timestamps(). Edit it to define your actual table columns before running php dgz migrate.


    LAZY LOADING RELATED DATA
    --------------------------

      Once you have set up $_hasChild and $_hasParent on your models, Dorguzen gives you a powerful
      ability called LAZY LOADING. This means you can fetch related records from another table by simply
      calling a method named after that related model — without writing any SQL or any extra query code
      yourself. Dorguzen handles everything in the background.


      WHAT IS LAZY LOADING?

      "Lazy" in this context does not mean slow or careless. It means the related data is only fetched
      from the database at the exact moment you ask for it — not before. This is efficient because you
      only pay the cost of a database query when you actually need the data.


      HOW TO USE IT — STEP BY STEP

      Step 1: Load a specific record into your model using loadData($id).
              This tells Dorguzen which record you are working with. It populates the model's internal
              data array with that record's fields and values, and returns the model instance itself
              so you can chain the next call directly.

                  $user = container(Users::class)->loadData(5);

      Step 2: Call the related model's name as a method on that model instance.

              To fetch children (one-to-many); call the child model name as a method.
              This returns an ARRAY of all matching child records.

                  $posts    = $user->posts();     // returns all posts belonging to user 5
                  $comments = $user->comments();  // returns all comments belonging to user 5

              To fetch parents (many-to-one); call the parent model name as a method.
              This returns a single ARRAY representing the one parent record.

                  $department = $user->department();  // returns the department this user belongs to


      WHAT IS RETURNED?

      - A hasChild call (fetching children) always returns an ARRAY of rows. Each row is itself an
        associative array of column names and values. If no children exist, an empty array [] is
        returned — never false or null.

      - A hasParent call (fetching a parent) returns a single associative array representing the one
        parent row, or false if no matching record was found.


      THE NAMING RULE — HOW TO NAME YOUR METHOD CALL

      This is the most important thing to understand about lazy loading in Dorguzen. The method name
      you call must match the CLASS NAME of the related model. The comparison is CASE-INSENSITIVE,
      which means you have complete freedom in how you capitalise the method call.

      Example: if your child model class is named 'Posts', all of the following will work:

          $user->posts()
          $user->Posts()
          $user->POSTS()

      They all resolve to the same model because Dorguzen converts both the method name and the class
      name to lowercase before comparing them.

      What about longer or unusual class names? Suppose your posts model is not simply called 'Posts'
      but has the more unusual name 'TheirCrazyPosts'. In this case, ALL of the following are
      equivalent and will work correctly:

          $user->theirCrazyPosts()     // matches 'TheirCrazyPosts' — recommended, most readable
          $user->theircrazyposts()     // also matches — all lowercase is fine
          $user->THEIRCRAZYPOSTS()     // also matches — all uppercase is fine too
          $user->TheirCrazyPosts()     // also matches — identical capitalisation to the class name

      The recommended style is to write the method name in camelCase (first letter lowercase, rest
      matching the class name), which is the standard PHP convention for method calls:

          $user->theirCrazyPosts()     // recommended

      IMPORTANT: the method name must match the MODEL CLASS NAME, not the database table name. Dorguzen
      looks up the model class in $_hasChild or $_hasParent and then works out the table name from
      the model itself. So if your class is named 'TheirCrazyPosts' but its table is named 'posts',
      you still call $user->theirCrazyPosts() — never $user->posts().


      WHAT HAPPENS IF YOU CALL A METHOD THAT DOES NOT EXIST?

      If you call a method on a model and the name does not match anything in $_hasChild or $_hasParent,
      Dorguzen will throw a BadMethodCallException with a clear message. This helps you catch typos
      immediately rather than silently returning empty data.

          $user->blahBlah();
          // throws: BadMethodCallException: Call to undefined method Users::blahBlah()


      WHAT HAPPENS IF YOU FORGET TO CALL loadData() FIRST?

      If you try to lazy-load children without having first loaded a record, Dorguzen cannot know which
      record's children to fetch — it needs the primary key value of the current record. In this case
      it throws a RuntimeException with a message telling you exactly what to do.

          $user = container(Users::class);   // model is empty — no record loaded yet
          $user->posts();
          // throws: RuntimeException: Cannot lazy-load 'posts': no primary key value is loaded on
          //         Dorguzen\Models\Users. Call loadData($id) first to load a record into the model.



      FULL WORKING EXAMPLE
      ----------------------
      Imagine a blog application. The Users model declares Posts as a child, and the Posts model
      declares Users as a parent.

      In the Users model:
          protected $_hasChild = [
              Posts::class => 'post_author_id',
          ];

      In the Posts model:
          protected $_hasParent = [
              Users::class => 'post_author_id',
          ];

      In your controller or service:

          // Load user with ID 3, then fetch all their posts
          $user  = container(Users::class)->loadData(3);
          $posts = $user->posts();

          foreach ($posts as $post) {
              echo $post['post_title'];
          }

          // Load post with ID 12, then fetch its author
          $post   = container(Posts::class)->loadData(12);
          $author = $post->users();

          echo $author['users_name'];


      HOW DORGUZEN RESOLVES THE CALL INTERNALLY (FOR THE CURIOUS)

      When you call $user->posts(), Dorguzen follows these steps:

        1. PHP detects that 'posts()' is not a real defined method on the Users model.
        2. PHP automatically calls the __call() magic method on DGZ_Model, passing 'posts' as the name.
        3. Dorguzen loops through the $_hasChild array on Users, extracting just the short class name
           from each key (e.g. 'Posts' from Dorguzen\Models\Posts) and lowercases it.
        4. It finds that 'posts' matches 'posts' (from Posts::class lowercased).
        5. It reads the foreign key field name from the array value (e.g. 'post_author_id').
        6. It reads the primary key value of the currently loaded user record from the model's data.
        7. It resolves the Posts model instance from the DI container.
        8. It runs: SELECT * FROM posts WHERE post_author_id = {user's PK value}
        9. It returns the result as an array of rows.

      The class resolution in step 7 supports both full class names (FQCNs like Dorguzen\Models\Posts)
      and plain short strings like 'Posts' — Dorguzen will find the right registered class either way.




      THE DORGUZEN MIGRATION SYSTEM
      ---------------------------------
  The Dorguzen migration system is responsible for:

Creating tables

Modifying schema

Tracking which migrations have already run

Preventing concurrent migration execution

Supporting rollbacks

Resetting databases safely

It is composed of:

Infrastructure Tables:

    dgz_migrations
    dgz_migration_locks

Core Classes:

    Blueprint
    Schema
    Migration
    MigrationRepository
    MigrationLockRepository


⚠️ Infrastructure Requirement
---------------------

For the migration system to function, the following tables must already exist:

  dgz_migrations
  dgz_migration_locks

You do NOT create migration files for these tables.
They are infrastructure tables created automatically by the migration system itself.
They are protected and never dropped by normal reset operations.

1️⃣ The Infrastructure Tables

There are two dgz_migrations and dgz_migration_locks. You do not have to create them. Dorguzen will create them for you. This is just an explanation of how they work.

a) The migrations table

    dgz_migrations

  This table tracks which migrations have already been executed.

  Structure:

    id (INT AUTO_INCREMENT PRIMARY KEY)
    migration (VARCHAR)
    batch (INT)
    created_at (TIMESTAMP NULL CURRENT_TIMESTAMP)

  Here is what happens in this table: 
    -It prevents migrations from running more than once.
    -It groups migrations into batches.
    -it enables rollbacks per batch.


b) The migrations_lock table

    dgz_migration_locks

  This table prevents two migration processes from running simultaneously.

  Structure:

    id (INT PRIMARY KEY)
    locked_at (DATETIME NULL DEFAULT NULL)

  Only ONE row is ever used:

  id = 1

  The way it works is the system will record a migration is is currently running here so it is not 
  run by any other script. It deletes it from here when its done and marks the migration file as ran
  in the migrations table. 
  Note carefully that this id field is not incremented, because everytime a record is inserted in 
  here, its value will always be 1. 

  This is a database-level mutex.


2️⃣ Migration Execution Flow (Internal Mechanics)

When migrations are executed:

Step 1 — Ensure Infrastructure Tables Exist

  Both repositories call:

      ensureTableExists()

  This guarantees that:

    dgz_migrations exists
    dgz_migration_locks exists

  Again, these two tables will be created if they do not exist already, which means you could create them yourself, but you do not have to. It's recommended to let the system create them for you, as you may get the fields and their types wrong.


Step 2 — Acquire Lock

  MigrationLockRepository->acquire()

  It attempts:

      INSERT INTO dgz_migration_locks (id, locked_at)
      VALUES (1, CURRENT_TIMESTAMP)

  If it fails:
    -It means another migration process is running
    -So the execution is stopped
    -and a RuntimeException thrown

  This prevents concurrent migrations.


Step 3 — Determine Which Migrations Have Run

      MigrationRepository->getRan()

  This fetches:

      SELECT migration FROM dgz_migrations

  Any migration file already recorded here will NOT be re-executed.


Step 4 — Run New Migrations

  For each new migration:
    -Instantiate migration class
    -The up() method of the migration will be called
    -The SQL statements of the migration via addStatement() is collected
    -Each of the SQL statements is executed
    -The specific migration is then inserted into the dgz_migrations table, thereby logging it as ran 


Step 5 — Release Lock

After execution:

    MigrationLockRepository->release()

  Which runs:

      DELETE FROM dgz_migration_locks WHERE id = 1

  The migration file that was being run is now unlocked again.


3️⃣ The Migration Base Class

Every migration class extends:

    the Migration abstract class 

  The Migration class provides:

      $this->schema (Schema instance)
      $this->addStatement()
      $this->getStatements()

  You must implement:

  public function up(): void
  public function down(): void

  Here is how they work: The up() and down() methods dO NOT execute SQL directly.
  Instead, they do this to collect their SQL statements:

      $this->addStatement($sql);

  The migration runner later executes the collected SQL statements.

  This allows for:
    -Controlled execution
    -Logging
    -the possibility of rollbacks
    -Dry runs (future possibility)


4️⃣ The Schema Class

  Schema (the class) is responsible for:
    -converting Blueprint definitions into SQL.
    -Creating Tables eg

        $sql = $this->schema->create('users', function (Blueprint $table) {
            ...
        });

  This Schema class:
    -Instantiates Blueprint
    -Passes it to your callback
    -Calls $blueprint->toSqlCreate()
    -Returns the final SQL string

  It does NOT execute it automatically.

  You must use addStatement(...) to prepare the SQL for that method (up() or down()) to be queued:

      $this->addStatement($sql);

  Dropping tables
  -----------
  The Schema class is also responsible for dropping Tables. This happens in the down() method of the 
  migration class. For example:

      public function down(): void
      {
          // rollback
          $sql = $this->schema->dropIfExists('users');
          $this->addStatement($sql);
      }


5️⃣ Blueprint — The Table DSL

Blueprint is your schema builder DSL.

It generates SQL for:

      CREATE TABLE `table` (...)
      ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
      Column Types Available

How to generate database field schemas:

  Primary Key
      $table->id();
    Creates:
      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

    You can customize it like this:

      $table->id('user_id');
      Custom Primary Key
      $table->primaryKey('uuid');

    Which will create:
      VARCHAR(255) PRIMARY KEY

    This will be useful for UUIDs

  Composite key patterns

    Foreign ID (No constraints yet)
    $table->foreignId('user_id');

    Creates:

      INT UNSIGNED
      (Foreign key constraints may be added later.)

  String
      $table->string('name');
      $table->string('name', 100);
    Creates:
      VARCHAR(255)
      VARCHAR(100)

  Integer
      $table->integer('age');
      $table->unsignedInteger('score');

  Decimal
      $table->decimal('price', 10, 2);
    Creates:
      DECIMAL(10,2)
      
  Enum
      $table->enum('status', ['pending', 'approved', 'rejected']);
    Creates:
      ENUM('pending','approved','rejected') NOT NULL

  Text Types
      $table->text('bio');
      $table->longText('content');

  TEXT (64KB)
      LONGTEXT (~5GB)

  Dates & Timestamps
      $table->date('birth_date');
      $table->timestamp('verified_at');
      $table->timestamps();

      timestamps() adds:
        created_at
        updated_at

  Unique Index
      $table->unique('email');
    Adds:
      UNIQUE (`email`)



6️⃣ Creating a Migration File

To create a migration, run this command:

      php dgz make:migration create_users_table

This will generate a migration file stub for you to easily edit:

  Tip: if you are creating a model and a migration together, you can do both in one command using
  make:model with the -m flag — see the "CREATING A MODEL (CLI)" section above.

      php dgz make:model Products -m                       // auto-names the migration create_products_table
      php dgz make:model Products -m create_products_table // same, but with an explicit migration name

      database/migrations/YYYY_MM_DD_HHMMSS_create_users_table.php

Example Migration File
<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $sql = $this->schema->dropIfExists('users');
        $this->addStatement($sql);
    }
};



7️⃣ What Happens When You Run Migrations

Let's say you run migrations. Internally, here is the process that runs:

  -Lock is acquired (meaning the script sets a value in the dgz_migration_locks table to signal to all other 
    migrations scripts that it is already running, so they should wait for it to finish. Every migration 
    script does the same thing, but when trying to set the lock, the inserting will basically fail if 
    a lock record set by another script is already there. This is because only one record is ever set in 
    the dgz_migration_locks table, and its id is always the same, 1).  
  -It checks the dgz_migrations table to see if that migratrion file has not already been ran
  -If it is determined that it is a new migration
    -The migration's up() called
    -which collects all the SQL of that migration
    -The SQL statements are executed
    -That migration file is then logged as ran-in dgz_migrations like so:

        INSERT INTO dgz_migrations (migration, batch, created_at)

  -The lock is released (meaning the entry earlier set in dgz_migration_locks is deleted).



8️⃣ Batches Explained

Each migration run increments batch number. For example:

First run:

  Batch 1:
  - create_users_table
  - create_posts_table

Secondly, run:

  Batch 2:
  - add_status_to_users

Rollback will remove entire last batch.



9️⃣ Rollbacks

When rolling back, the last batch is fetched for each migration in reverse order:

    getLastBatchMigrations()

Call down()

This method 
  -Runs the SQL in that migration's down() method which drops the table that migration creted in up() 
  -it then removes the entry of that migrations from the dgz_migrations table



🔟 Resetting Database

    dropAllNonInfrastructureTables()

Drops all tables except:

    dgz_migrations
    dgz_migration_locks

This allows:
  -Clean rebuilds
  -Safe resets
  -Infrastructure preservation



1️⃣1️⃣ Why the Lock System Matters

Without locks:

Two developers could run migrations simultaneously

Tables could partially create

Schema corruption possible

With dgz_migration_locks:

Only one migration process runs

Guaranteed safe schema changes



1️⃣2️⃣ Key Design Decisions

Dorguzen migrations:

✔ Are SQL-first
✔ Are explicit
✔ Collect statements before execution
✔ Prevent concurrent execution
✔ Track batches
✔ Support full rollback
✔ Protect infrastructure tables



1️⃣3️⃣ Mental Model for Developers

When writing a migration:

You are NOT writing raw SQL.

You are:

Using Blueprint DSL

Generating SQL

Adding statements

Letting Dorguzen execute safely

Logging migration

Final Summary

The Dorguzen Migration System provides:

Infrastructure tracking (dgz_migrations)

Concurrency safety (dgz_migration_locks)

Blueprint DSL for table creation

SQL collection system via Migration

Safe batch-based rollback

Full database reset options

Production-safe locking

Developers only need to:

php dgz make:migration create_table

Then:

Edit file

Use Blueprint

Run migrations

And Dorguzen guarantees safe schema evolution.







      RUNNING MIGRATIONS (CLI Commands)
      ----------------------------------
  Dorguzen provides CLI commands to:

Run all pending migrations

Run a specific migration file

Roll back migrations

Reset the database

These commands work together with:

MigrationRepository

MigrationLockRepository

Migration

Schema

Blueprint

1️⃣ Run All Pending Migrations
php dgz migrate
What This Does

Ensures infrastructure tables exist:

    dgz_migrations
    dgz_migration_locks

Acquires migration lock.

Reads all files inside the directory:

    database/migrations/

Compares them against:

    dgz_migrations

Runs only migrations that have NOT been logged.

Logs each migration into dgz_migrations.

Releases the lock.

Important

If a migration already exists in dgz_migrations, it will NOT run again.

This prevents accidental duplicate execution.

2️⃣ Run a Specific Migration File

php dgz migrate --file=2025_01_01_000000_create_users_test_table.php

What This Does

Executes only the specified migration file.

Does NOT run others.

Still respects locking.

Still logs into dgz_migrations.

Will NOT re-run if already logged.

This is useful when:

Testing one migration

Developing incrementally

Debugging schema issues

3️⃣ Roll Back Last Batch
php dgz migrate:rollback
What This Does

Finds the highest batch number in dgz_migrations.

Retrieves all migrations in that batch.

Runs their down() methods in reverse order.

Deletes those migration records from dgz_migrations.

Example

If your dgz_migrations table contains:

migration	batch
create_users	1
create_posts	1
add_status	2

Running:

php dgz migrate:rollback

Will:

Roll back add_status

Remove it from table

Leave batch 1 intact

4️⃣ Reset All Migrations
php dgz migrate:reset
What This Does

Rolls back ALL batches.

Calls down() on every migration.

Clears dgz_migrations.

This returns the database to a clean state.

Infrastructure tables remain intact.

5️⃣ Fresh Rebuild (Drop & Re-run)
php dgz migrate:fresh
What This Does

Drops all non-infrastructure tables.

Clears migration log.

Runs all migrations from scratch.

It uses:

MigrationRepository->dropAllNonInfrastructureTables()

Protected tables:

dgz_migrations
dgz_migration_locks

This is extremely useful during development.

6️⃣ Migration Status
php dgz migrate:status

Shows:

All migration files

Whether they have run

Batch numbers

This reads from:

dgz_migrations
7️⃣ How Batching Works (Clarified)

Each time you run:

php dgz migrate

All new migrations are grouped into ONE new batch.

Batch numbers increment automatically.

Rollback removes only the latest batch.

This gives safe incremental rollbacks.

8️⃣ What Happens If Migration Fails Midway?

Because Dorguzen:

Uses locking

Logs only after execution

If a migration throws an exception:

Execution stops

Lock is released

Migration is NOT logged

It can be safely rerun

This protects schema integrity.

9️⃣ Recommended Developer Workflow
Creating a new table
php dgz make:migration create_orders_table

Edit file in:

database/migrations/

Then:

php dgz migrate
Undo last change
php dgz migrate:rollback
Rebuild entire schema
php dgz migrate:fresh
🔟 Production Best Practice

In production environments:

Always run php dgz migrate during deployment.

Never use migrate:fresh.

Always ensure backups exist before rollback.

Lock system ensures safe single-process execution.

Final Summary of Commands
Command	Purpose
php dgz migrate	Run all pending migrations
php dgz migrate --file=...	Run specific migration
php dgz migrate:rollback	Roll back last batch
php dgz migrate:reset	Roll back all batches
php dgz migrate:fresh	Drop all tables & re-run
php dgz migrate:status	Show migration state








      DATABASE SEEDING
      -----------------

    What Is Database Seeding?

Database seeding is the process of automatically filling your database with data.

Instead of manually inserting rows into your tables, Dorguzen allows you to:

Generate realistic test data

Populate demo environments

Prepare development databases

Recreate consistent test datasets

Think of it like planting seeds 🌱 — your database grows with useful content automatically.

The Seeding Architecture (Simple Overview)

Dorguzen's seeding system has 4 layers:

    CLI Command
        ↓
    SeederRunner
        ↓
    Seeder Classes
        ↓
    Factories + Pools


Let's break this down in simple terms.



  CLI Commands
  ----------

Everything starts from the command line.

Basic command
php dgz db:seed


This runs the main seeder class called:

DatabaseSeeder

Run a Specific Seeder

    php dgz db:seed --class=UserSeeder


This runs only UserSeeder.

Run All Seeders Explicitly

    php dgz db:seed --all


This forces execution of DatabaseSeeder.

Preview Without Running (Pretend Mode)

    php dgz db:seed --pretend


This shows what would happen — but does not insert anything.

This is safe for testing.

Force Seeding in Protected Environments

    php dgz db:seed --force


Some environments (like production) are protected.

    --force overrides that safety check.

Fresh Migration + Seed

    php dgz migrate:fresh --seed


This:

Drops all tables

Re-runs all migrations

Seeds the database

Perfect for starting over.



  Seeder Classes
  ----------

A Seeder is a class responsible for inserting data into a specific table.

Example:

    class UserSeeder extends Seeder
    {
        protected string $table = 'users';

        public function run(): void
        {
            UserFactory::new()->count(50)->create();
        }
    }


Simple meaning:

“Create 50 users.”

The Main Seeder: DatabaseSeeder

This is your master orchestrator.

    class DatabaseSeeder extends Seeder
    {
        public function run(): void
        {
            $this->call(UserSeeder::class);
            $this->call(PostSeeder::class);
        }
    }


This ensures seeders run in the correct order.



  Factories — Automatic Record Generation
  ----------

Factories generate rows for your tables.

Example:

    class PostFactory extends Factory
    {
        protected function definition(): array
        {
            return [
                'title' => Pool::get('text.sentence', 6),
                'body'  => Pool::get('text.paragraph', 4),
                'created_at' => Pool::get('date.now'),
                'updated_at' => Pool::get('date.now'),
            ];
        }
    }


This defines what a “Post” looks like.

When you run:

    ostFactory::new()->count(10)->create();


Dorguzen will generate 10 posts automatically.



  Data Pools — Smart Fake Data
  ----------

Pools generate realistic fake data.

Instead of hardcoding text like:

'title' => 'Sample Post'


You can do:

'title' => Pool::get('text.sentence', 6)


And Dorguzen will generate a sentence automatically.

Available Pools

Here are some examples of built-in pools:

    Text
    Pool::get('text.sentence', 6)
    Pool::get('text.paragraph', 3)
    Pool::get('text.text', 200)

    Names & Emails
    Pool::get('name.full')
    Pool::get('email', $name)

    Numbers
    Pool::get('number.int', 1, 100)
    Pool::get('number.float', 0, 100, 2)
    Pool::get('number.numeric', 6)

    Dates
    Pool::get('date.now')

    Status & Flags
    Pool::get('status')
    Pool::get('boolean')


This keeps your factories clean and readable.


  Factory Lifecycle Hooks
  ----------

Factories support lifecycle hooks.

    beforeCreate()

Modify attributes before inserting:

    protected function beforeCreate(array &$attributes): void
    {
        $attributes['slug'] = strtolower(str_replace(' ', '-', $attributes['title']));
    }

    afterCreate()


Run logic after insertion:

    protected function afterCreate(array $attributes): void
    {
        // attach relationships
    }



  Unique Values
  ----------

Factories support unique generation.

Example:

$email = $this->unique('email', function () use ($name) {
    return Pool::get('email', $name);
});


This prevents duplicate values in the database.



 Environment Protection
 ----------

Dorguzen protects important environments.

If seeding is attempted in a protected environment, it will throw an error unless:

    --force


This prevents accidental data corruption.

Here is how everything works together. When you run:

    php dgz db:seed


Here's what happens internally:

  -CLI reads your options
  -SeederRunner initializes
  -DatabaseSeeder runs
  -Individual seeders (your custom seeders in database/seeders/) execute
  -Factories (called from your custom seeders) generate records
  -Pools (that you use in your factories) generate fake data
  -Database is populated

This all happens automatically when you run the seed command

  php dgz db:seed



The Dorguzen seeding system is designed to be:

Deterministic
Environment-aware
CLI-driven
Extensible
Cleanly layered
Dependency-free (no external faker libraries)

It is designed to scale from small hobby projects to Professional applications
and even Large systems.




When Should You Use Seeding?
------------------------
Use seeding when:

  -Starting a new development environment
  -Resetting a test database
  -Preparing demo data
  -Running automated tests
  -Onboarding new developers


If you remember only three things, let it be these:

  -Factories define how a table row looks.
  -Seeders decide how many rows to create.
  -CLI runs everything.





  The SuperAdminSeeder — Your First Login
  ----------------------------------------

  Dorguzen ships with a SuperAdminSeeder that creates the initial super admin account your
  application needs from day one. This seeder is registered in DatabaseSeeder and is safe
  to run at any time — it uses INSERT IGNORE, so running it more than once will never
  create a duplicate record.

  Default super admin credentials
  --------------------------------

      First name : Dorguzen
      Last name  : Admin
      Email      : admin@dorguzen.com
      Password   : Admin123

  Use these credentials to log in for the first time. Once you are logged in, go to the
  admin dashboard and update the super admin's details (name, email, and password) to
  values specific to your application before going live.

  Changing the password can be done from Admin > Manage Users > Edit User (or use the
  "Admin Change Password" option in the admin dashboard).

  Running migrations and the seeder
  ----------------------------------

  After setting up your database and running your migrations, run the super admin seeder
  immediately so the account is available before you open the app in a browser:

      # 1. Run all migrations first
      php dgz migrate

      # 2. Seed the database (creates the super admin account)
      php dgz db:seed

  Or, if you want to reset everything from scratch:

      php dgz migrate:fresh --seed

  The seeder is idempotent — it is safe to run it multiple times.


  User Roles in Dorguzen
  -----------------------

  Dorguzen handles roles through the users_type field in the users table. There are four
  built-in roles:

      super_admin   The highest-privilege user. Has access to everything, including
                    actions that are restricted from admin and admin_gen users. There
                    should normally be only one super admin per application.

      admin_gen     A general administrator. Has broad admin access but cannot perform
                    super-admin-only actions.

      admin         A standard administrator. Has admin panel access within the scope
                    assigned to them (e.g. managing a specific location or section).

      member        A regular registered user. Has access only to the member-facing
                    dashboard and their own account features.

  In views and controllers you can check the current user's role using the Auth() helper:

      Auth()->role()            // returns the users_type string, e.g. 'super_admin'
      Auth()->isAdmin()         // true if admin, admin_gen, or super_admin
      Auth()->check()           // true if the user is authenticated (any role)
      Auth()->guest()           // true if the user is NOT authenticated

  Example — restrict a block of UI to super admins only:

      <?php if (Auth()->role() === 'super_admin'): ?>
          <a href="...">Delete User</a>
      <?php endif; ?>

  Roles are not enforced by a separate permissions table — they are a simple string value
  that your controllers and views inspect. This keeps the system lightweight and easy to
  extend if you need to add custom roles in the future.


  Dorguzen's support for multiple DB drivers
          ------------------------------- 
  SQLite Support & Swappable Database Drivers. Dorguzen provides a fully swappable database layer. Developers can 
switch between supported database engines without changing application code — only configuration.
As of v1, Dorguzen officially supports:

✅ MySQLi (native MySQL driver)
✅ PDO (MySQL via PDO)
✅ SQLite (via PDO)
✅ PostgreSQL 

  This is how the current DB abstraction in Dorguzen works 
with the four drivers; Mysqli, SQLite, PDO and Postgres.         
  Here are the key files involved. They are 8 in number:  

DGZ_DB_Singleton.php (main glue)
DGZ_DBDriverInterface.php (the contract)

DGZ_MySQLiDriver.php
DGZ_PDODriver.php

DGZ_SQLiteDriver.php
DGZ_PostgresDriver.php

DGZ_DBAdapter.php (the bridge)
DGZ_Model.php (parent ORM model)

First of all, all Dorguzen (dgz) models gain DB access by extending the parent model DGZ_Model. 
Whenever DGZ_Model's connect() is called (which eventually all models will call if they need 
access to the DB), DGZ_Model pulls an instance of DGZ_DB_Singleton

    protected function connect()
    {
        $db = DGZ_DB_Singleton::getInstance();
        $this->hydrateSchemaIfNeeded();
        return $db;
    }

Here is what the hydrateSchemaIfNeeded() model looks like, including the chain of other methods 
being called in the process:

    protected function hydrateSchemaIfNeeded(): void
    {
        $class = static::class;

        if (!isset(self::$schemaCache[$class])) {
            $schema = $this->loadSchemaFromDatabase();
            self::$schemaCache[$class] = $schema;
        }

        $this->_columns = self::$schemaCache[$class];

        $this->validateModelData();
        $this->applyNullDefaults();
    }


    public function loadSchemaFromDatabase()
    {
        $db = DGZ_DB_Singleton::getInstance();

        $table = $this->getTable();

        // Load schema
        $schemaQuery = 'DESCRIBE ' . lcfirst($table);
        $columns = $db->query($schemaQuery);

        if (empty($columns)) {
            throw new RuntimeException(
                "ORM schema load failed for table '{$table}' (" . static::class . ")"
            );
        }

        $schema = [];

        foreach ($columns as $column) {
            $val = 's';
            if (preg_match('/int/', $column['Type'])) $val = 'i';
            if (preg_match('/decimal|float/', $column['Type'])) $val = 'd';

            $schema[$column['Field']] = $val;
        }

        return $schema;
    }


    protected function validateModelData(): void
    {
        foreach ($this->data as $setDataKey => $setDataValue)
        {
            if (!array_key_exists($setDataKey, $this->_columns))
            {
                throw new RuntimeException(
                    "Invalid ORM field '{$setDataKey}' on model " . static::class
                );
            }
        }
    }

    protected function applyNullDefaults(): void
    {
        foreach ($this->_columns as $column => $_type) {
            if (!array_key_exists($column, $this->data)) {
                $this->data[$column] = null;
            }
        }
    }


DGZ_DB_Singleton is the glue point between your application and the DB drivers. It checks 
the application's config for the defined DB driver type (DB driver) and connection credentials. 
It then loads the relevant driver instance which wil be either one of these:

  DGZ_MySQLiDriver
  DGZ_PDODriver
  DGZ_SQLiteDriver 
  DGZ_PostgresDriver

while passing the given credentials to the constructor of that DB driver. It then passes 
that driver object into an instance of DGZ_DBAdapter, which stores that DB driver on its 
'driver' property. It then returns the DGZ_DBAdapter instance back to DGZ_Model->connect() 
the caller. The current or active DB driver is therefore stored in the $driver property 
of DGZ_DBAdapter, the class that DGZ_Model and all its children will now be using. 
That is it, in a nutshell.

It's worthy to note, that all the DB drivers; currently DGZ_MySQLiDriver, DGZ_PDODriver,
DGZ_SQLiteDriver and DGZ_PostgresDriver implement the DGZ_DBDriverInterface which is the 
contract defining all the methods these drivers must implement.

The DB connection and queries that DGZ_Model now uses will all go through DGZ_DBAdapter. 

But here is where the magic happens; all these methods implemented by the DB drivers, 
which are defined by the contract (DGZ_DBDriverInterface) are also implemented by DGZ_DBAdapter. 
This is in order that, whenever a model in your application calls any of these methods, the 
DGZ_DBAdapter has the method, but internally, it passes the query to the active DB driver by 
calling that same method on that driver.




  1. Architecture Overview
  ------------------------

Dorguzen's database layer is built around 6 core files:

DGZ_DB_Singleton.php        (Main glue)
DGZ_DBDriverInterface.php   (Contract)
DGZ_MySQLiDriver.php
DGZ_PDODriver.php
DGZ_SQLiteDriver.php
DGZ_PostgresDriver.php
DGZ_DBAdapter.php           (Bridge)
DGZ_Model.php               (Parent ORM model)





  2. How Database Access Works
  ------------------------

Step 1 — Models Extend DGZ_Model

All models gain database access by extending DGZ_Model.
When a model needs DB access:

  protected function connect()
  {
      $db = DGZ_DB_Singleton::getInstance();
      $this->hydrateSchemaIfNeeded();
      return $db;
  }


Step 2 — Schema Hydration (Automatic ORM Mapping)

Before queries execute, Dorguzen introspects the database table:
protected function hydrateSchemaIfNeeded(): void


This calls:

  loadSchemaFromDatabase()

Which:

  -Loads table schema
  -Detects column types
  -Caches the schema
  -Validates model fields
  -Applies null defaults

This guarantees:

  -No invalid fields can be inserted
  -ORM and DB stay synchronized
  -Columns are auto-typed
  -Schema is cached per model class for performance.


Step 3 — The Singleton Glue Layer

  DGZ_DB_Singleton:

    -Reads .env credentials
    -Detects DB_CONNECTION
    -Instantiates the correct driver
    -Wraps it in DGZ_DBAdapter
    -Returns the adapter

Example flow:

  Model → DGZ_DB_Singleton
        → Creates Driver
        → Wraps in Adapter
        → Returns Adapter


Step 4 — The Adapter Pattern

  DGZ_DBAdapter is the bridge between:

    -Your application
    -The active database driver

All drivers implement:

  DGZ_DBDriverInterface


The adapter implements the same methods and internally forwards calls to the active driver:

    public function query(string $sql, array $params = [])
    {
        return $this->driver->query($sql, $params);
    }


This means:

Models never know which DB is active

Drivers can be swapped without code changes


3. Official Driver Support

Driver	      Status	      Notes
MySQLi	      ✅            Fully Supported	
PDO (MySQL)	  ✅            Fully Supported	
SQLite	      ✅            Fully Supported	
PostgreSQL	  ✅            Fully Supported	


4. query() vs execute() (Important Design Rule)

Dorguzen intentionally separates read and write operations.

query()

  -Recommended for SELECT queries ONLY
  -Returns array of rows
  -Does NOT track affected rows or last insert ID

  Example:

    $rows = $db->query("SELECT * FROM users WHERE id = ?", [$id]);

    
execute()

  -Recommended for INSERT / UPDATE / DELETE
  -Returns boolean
  -It tracks:

    -affected rows
    -The last insert ID (if applicable)

  Example:

    $success = $db->execute(
        "UPDATE users SET name = ? WHERE id = ?",
        [$name, $id]
    );

Why This Design?

  -Clean separation of concerns
  -No SQL parsing inside drivers
  -Predictable behavior
  -Easier maintenance


5. Getting the Last Inserted ID

After an INSERT using execute():

    $db->execute(
        "INSERT INTO users (name, email) VALUES (?, ?)",
        [$name, $email]
    );

    $id = $db->lastInsertId();


Works for:

  -MySQLi
  -PDO
  -SQLite
  -PostgreSQL (via RETURNING)


6. Environment Configuration (.env Setup)

Dorguzen uses .env to determine which database engine to load.

  Using MySQLi
    DB_CONNECTION=mysqli

    DB_HOST=127.0.0.1
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    DB_DATABASE=your_database
    DB_PORT=3306


    Requirements:

    -MySQL server installed (MAMP, XAMPP, Docker, etc.)
    -mysqli extension enabled


  Using PDO (MySQL via PDO)

    DB_CONNECTION=pdo
    DB_HOST=127.0.0.1
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    DB_DATABASE=your_database
    DB_PORT=3306


    Requirements:

    -MySQL server
    -pdo_mysql extension enabled


  Using SQLite

    DB_CONNECTION=sqlite
    DB_SQLITE_PATH=/absolute/path/to/storage/database.sqlite


Important:

SQLite uses a file path, NOT host/user/password.
The file must exist before connecting.

Create the file:

  touch storage/database.sqlite
  chmod 644 storage/database.sqlite


If in the Dorguzen testing environment, the setting in .env.testing should be like this:

  DB_CONNECTION=sqlite
  DB_SQLITE_PATH=:memory

  :memory: creates a temporary in-memory database for tests.
  In this testing setup, no MySQL server is required.



  Using PostgreSQL

    DB_CONNECTION=pgsql

    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=your_database
    DB_USERNAME=your_username
    DB_PASSWORD=your_password


    Requirements:

    -PostgreSQL installed locally or via Docker
    -pdo_pgsql extension enabled


  Example Docker setup:

    docker run --name pg-dorguzen \
      -e POSTGRES_PASSWORD=secret \
      -p 5432:5432 -d postgres


7. Switching Drivers

  You would switch DB drivers to change the active database engine.
  That is what this line in all the driver types above have been doing:

    DB_CONNECTION=

  The next thing is to update the .env file
  Then restart your application
  That is it; there are no code changes required.

This is the power of the Adapter + Interface pattern.


8. Schema Introspection Differences

  Dorguzen automatically maps your model to its databse table using its built-in Object Relational Mapper (ORM)
that works with the currently active databse driver once you load your application.

All drivers normalize column keys to:

Field
Type

So the ORM behaves consistently across engines.


9. Primary Key Flexibility

Dorguzen allows models to use any name for the primary key field of any table linked to a model.

But the default recommended name for the primary key field is:

id


If different, define it on the model by giving the model an 'id' (string) field, with its value the name of your 
model's primary key field.

Drivers dynamically detect primary key fields where supported.


10. Design Philosophy

Dorguzen enforces:

  An  MVC structure whereby 
    -the DB logic lives in Models
    -Controllers handle data logic
    -Views contain no database access
    -The database engine is configurable infrastructure

  This promotes:
    -Clean architecture
    -Testability
    -Portability
    -Engine independence


11. Testing Strategy

Once more, it is recommended:

  -to use SQLite :memory: for automated tests
  -To use MySQL for production


The following is the documentation of the public API of the database layer.





Database Driver API Reference
------------------------

  All Dorguzen database drivers implement:

DGZ_DBDriverInterface


These methods are accessed through the DGZ_DBAdapter, which is what your models receive when calling:

  $db = $this->connect();


You never interact with drivers directly — always through the adapter.

1️⃣ getTableSchema(string $table): array
Purpose

Used internally by the ORM to introspect table structure and map model fields.

Parameters
Parameter	Type	Description
$table	string	Database table name
Returns an array


An array describing table columns.

Used By

DGZ_Model::loadSchemaFromDatabase()

ORM schema hydration system

Should Developers Use It?

No. This is an internal ORM method.

2️⃣ connect()
Purpose

Returns the raw underlying database connection object.

for either of the following depending on the currently active driver in use:  

  -MySQLi driver → mysqli
  -PDO driver → PDO
  -SQLite → PDO
  -PostgreSQL → PDO

When To Use

Only for advanced scenarios (transactions, vendor-specific features).

Most applications should NOT need this.



3️⃣ prepare(string $query)
Purpose

Prepares a raw SQL statement.

Parameters
Parameter	Type	Description
$query	string	SQL statement
Returns

Driver-specific statement object.

When To Use

Rarely needed directly. Use query() or execute() instead.



4️⃣ query(string $sql, array $params = []): array
Purpose

Execute SELECT queries.

Design Rule

Recommended for SELECT statements ONLY.

Parameters
  Parameter	Type	            Description
  $sql	                      string	SQL SELECT statement
  $params	                    array	Bound parameters

Returns
  It returns an array of associative rows.

Example
    $db = $this->connect();

    $users = $db->query(
        "SELECT * FROM users WHERE status = ?",
        ['active']
    );

Returns
  [
      ['id' => 1, 'name' => 'John'],
      ['id' => 2, 'name' => 'Jane']
  ]

Why Not Use For INSERT?

Because query() is designed for returning rows, not mutation tracking.

5️⃣ execute(string $sql, array $params = []): bool
Purpose

Execute INSERT, UPDATE, DELETE queries.

Design Rule

Recommended for mutation queries ONLY.

Parameters
Parameter	Type	Description
$sql	string	SQL mutation statement
$params	array	Bound parameters

Returns
  It returns a boolean (True if execution succeeded, or False otherwise).

Example — Insert
    $db = $this->connect();

    $db->execute(
        "INSERT INTO users (name, email) VALUES (?, ?)",
        [$name, $email]
    );


Example — Update
    $db->execute(
        "UPDATE users SET name = ? WHERE id = ?",
        [$name, $id]
    );


6️⃣ lastInsertId(): int|string|null

Purpose

  It returns the last inserted primary key.

Example
    $db->execute(
        "INSERT INTO posts (title) VALUES (?)",
        [$title]
    );

    $id = $db->lastInsertId();


Works for:

  MySQLi
  PDO
  SQLite
  PostgreSQL (via RETURNING)


7️⃣ getAffectedRows(): int

Purpose
  It returns number of rows affected by the last mutation query.

Example

    $db->execute(
        "UPDATE users SET status = ? WHERE status = ?",
        ['inactive', 'active']
    );

    $count = $db->getAffectedRows();


Returns a number like

    3


Very useful for update/delete verification.


8️⃣ numRows($result): int

Purpose

  Returns number of rows in a result set.

  This is not reliable for SELECT in PDO-based drivers.

Example

    $rows = $db->query("SELECT * FROM users");


Normally you would simply do:

    $count = count($rows);


Recommended usage: avoid unless working with low-level result objects.



9️⃣ getPrimaryKeyField(string $table): ?string

Purpose:
  It returns the name of a table's primary key column.

Example

    $pk = $db->getPrimaryKeyField('users');


Returns:

  It returns a string of what the primary key field of the modfel is, for example:
    
    id


Used internally by ORM and Postgres RETURNING logic.



🔟 prepareInsertOrUpdate(array $data, array $passwordFields, string $type = 'insert')

Purpose:
  It builds SQL fragments for INSERT and UPDATE queries.
  It is used internally by DGZ_Model::save() and updateObject().

Parameters
Parameter	                  Description
$data	                      Field-value pairs
$passwordFields	            Fields requiring encryption
$type	                      insert or update

Returns
  It returns an array like this:
  
    [$fields, $placeholders, $values]


Example internal usage:

      list($fields, $placeholders, $values) = 
          $db->prepareInsertOrUpdate($data, ['password'], 'insert');


Developers do NOT call this directly.



11️⃣ encryptPasswordCondition(string $field): string

Purpose:
  It builds encrypted WHERE condition for password comparisons (MySQL AES_ENCRYPT).

Example internal usage:

    WHERE password = AES_ENCRYPT(?, ?)

  It is however only relevant for the MySQL driver.

Practical Usage Example Inside a Model:

    class User extends DGZ_Model
    {
        protected string $table = 'users';

        public function findActiveUsers()
        {
            $db = $this->connect();

            return $db->query(
                "SELECT * FROM users WHERE status = ?",
                ['active']
            );
        }

        public function deactivateUser(int $id): bool
        {
            $db = $this->connect();

            $db->execute(
                "UPDATE users SET status = ? WHERE id = ?",
                ['inactive', $id]
            );

            return $db->getAffectedRows() > 0;
        }
    }

Internal vs Public Methods Summary
Method	                      Public Use	                  Internal
query	                        ✅                            Yes	
execute	                      ✅                            Yes	
lastInsertId	                ✅                            Yes	
getAffectedRows	              ✅                            Yes	
connect (Advanced)
getTableSchema	              ❌                            ORM only	
prepareInsertOrUpdate	        ❌                            ORM only	
encryptPasswordCondition	    ❌                            Driver only	

Dorguzen enforces:

Clear separation between read and write queries
  -There is no SQL parsing in drivers
  -Driver swappability via adapter
  -ORM-driven schema validation
  -Environment-based DB switching





  Neo4j Graph Database Support in Dorguzen
  -----------------------------------------

  Dorguzen v1 introduces optional support for Neo4j, a popular graph database, allowing developers to harness the 
full power of Cypher queries without limiting flexibility or imposing an ORM-style abstraction.
This section explains how Neo4j is integrated, how to use it, and the design philosophy behind the implementation.


  Philosophy
  -----------

By design, Dorguzen does not abstract graphs into models or attempt to rewrite the Cypher Query Language. Instead, 
the framework exposes a Neo4j client directly to the developer. 
This approach allows developers to:

  -Write any Cypher query they need.
  -Fully leverage graph-specific capabilities such as relationships, traversals, pathfinding, aggregations, 
    and APOC procedures.
  -Use transactions where needed.
  -Avoid learning a proprietary or framework-specific query builder.

This ensures maximum flexibility while keeping Dorguzen lightweight and non-opinionated regarding graph modeling.


  Installation
  -----------

Neo4j support is optional. To install the Neo4j PHP client via Composer:

composer require laudis/neo4j-php-client:^3.4

In Dorguzen's composer.json, Neo4j is marked in the "suggest" block:

  "suggest": {
      "laudis/neo4j-php-client": "Required for Neo4j graph support. Install ^3.4"
  }

If Neo4j is not needed for a project, it can be completely omitted without affecting other Dorguzen functionality.


  Configuration
  -----------

Add Neo4j credentials in your .env file:

    NEO4J_URI=bolt://127.0.0.1:7687
    NEO4J_USERNAME=neo4j
    NEO4J_PASSWORD=your_password_here


Capture them in configs/database.php:

    'Neo4jCredentials' => [
        'uri' => env('NEO4J_URI'),
        'username' => env('NEO4J_USERNAME'),
        'password' => env('NEO4J_PASSWORD'),
    ],



  Core Integration
  ----------------

Dorguzen provides a thin wrapper client at:

  core/database/graph/DGZ_Neo4jClient.php


This class:
  -Connects to Neo4j using the official laudis/neo4j-php-client.
  -Provides a run() method for executing Cypher queries and returning normalized array results.
  -Supports transactions via a transaction() method for safe write operations.
  -Normalizes results, including Node and Relationship objects, into plain PHP arrays for easy handling.

Example Usage

    use Dorguzen\Core\Database\Graph\DGZ_Neo4jClient;

    $config = container(Config::class);
    $neo4jConn = $config->getConfig('database.Neo4jCredentials');

    $neo = new DGZ_Neo4jClient($neo4jConn);


    // Simple query
    $result = $neo->run('RETURN 1 AS test');
    print_r($result);


    // Create a new node
    $neo->run('CREATE (u:User {name: $name}) RETURN u', ['name' => 'Alice']);


    // Update a node
    $neo->run(
        'MATCH (u:User {name: $name}) SET u.updated = true RETURN u',
        ['name' => 'Alice']
    );


    // Transaction example
    $neo->transaction(function ($tx) {
        $tx->run('CREATE (n:Task {title: "Finish Dorguzen"})');
    });


  Optional and Modular Design
  -------------------------

  Optional installation: Neo4j support is not required for Dorguzen to function.

Easy removal: If installed but not needed, the package can be removed from Composer and the 
DGZ_Neo4jClient class will not be referenced, preventing errors.

No changes to MVC: Controllers and other models continue to operate normally. Developers simply use the Neo4j 
client when graph operations are required.


  Summary
  -------
Dorguzen exposes Neo4j via a thin client wrapper, preserving full Cypher flexibility.
No ORM abstraction is imposed—developers can fully leverage graph capabilities.
Installation is optional, modular, and easily removable.
Transactions and result normalization are supported.
Works seamlessly alongside other relational DB drivers in a hybrid architecture.
This approach gives developers freedom and power while keeping Dorguzen lightweight and modular, aligning perfectly 
with the framework's philosophy.





  


——————————————————————
	DATES AND TIME
——————————————————————

-This has to do with how dates and time 
  and managed in your application, including timezone configurations and formatting of the date and time values displayed to users.






——————————————————————
	FRONTEND/UI AND THE URL
——————————————————————

  This is about the ability of your application to pass data to and from the frontend, via URL parameters, template engines, redirecting to different views, and the generation of HTML assets for view file layouts. These also involves the ability of the programming language to manipulate these elements on the fly.
  The Javascript programming language 
 really shines in this domain. 
  This also covers everything the users sees on the screen (UI), and the manipulation thereof, programmatically whether it be through printing data to the screen, how to create comments, passing data to the view in the case of development frameworks, passing to or retrieving data from the URL etc.


Templating Engines — What They Are, Why Dorguzen Does Not Need One, and How to Add One If You Want
----------------------------------------------------------------------------------------------------

What templating engines solve
-------------------------------

In traditional PHP frameworks, view files are loose .php files that mix HTML markup with PHP
logic directly inline:

  <!-- Laravel Blade example -->
  <h1>{{ $user->name }}</h1>
  @if ($user->isAdmin())
      <a href="/admin">Dashboard</a>
  @endif

This works, but the separation between logic and presentation depends entirely on developer
discipline. Nothing stops a developer from querying the database, running business logic, or
calling framework internals right inside a view file. Over time these files tend to accumulate
logic they were never meant to contain.

Templating engines like Twig, Blade, and Latte were created to solve this:

  -They restrict what code can run inside a template (no arbitrary PHP, only expressions and
   control structures the engine explicitly allows).
  -They provide auto-escaping to prevent XSS — template variables are HTML-encoded by default.
  -They offer template inheritance via block/extend systems, so a base layout template can
   define regions that child templates fill in.
  -They separate the concerns of "prepare data" (controller) from "display data" (template),
   making views easier to hand to designers who do not write PHP.

Why Dorguzen does not need a templating engine
-----------------------------------------------

Dorguzen's view system already provides the same guarantees through a different mechanism:
class-based views.

In Dorguzen, every view is a PHP class with a show() method:

  class ProductsView extends DGZ_HtmlView
  {
      public function show(): void
      {
          ?>
          <h1><?= htmlspecialchars($this->controller->pageTitle) ?></h1>
          <?php
      }
  }

This enforces the separation that templating engines achieve through syntax restrictions, but
through PHP's own class structure instead. Specifically:

  -Logic belongs in the controller. The view class receives a reference to the controller and
   accesses prepared data from it — it does not run queries, call services, or make decisions.
   If it tried to, the MVC contract would be violated in an obvious, reviewable way.

  -Template inheritance is handled by DGZ_Layout. The layout class (seoMasterLayout, etc.)
   defines the outer shell — head, header, footer, nav. Views fill in only the content region.
   This is structurally equivalent to a Twig {% extends %} / {% block %} relationship, but
   implemented with PHP classes and the setContentHtml() / display() pipeline.

  -Auto-escaping is the developer's responsibility in Dorguzen views, just as it is in any
   raw PHP code. The standard PHP function htmlspecialchars() is used where needed. This is
   not a shortcoming unique to Dorguzen — it applies equally to any PHP code outside a
   templating engine.

  -Designer-friendly syntax is a non-goal for Dorguzen. Dorguzen targets PHP developers.
   Views are PHP classes, and PHP developers are already comfortable reading and writing them.

The bottom line: templating engines exist to bring discipline to loose PHP view files. Dorguzen
view files are not loose — they are typed, namespaced PHP classes inside a strict MVC pipeline.
The discipline is already built into the structure.


How to add Twig if you want it anyway
---------------------------------------

Dorguzen's rendering pipeline makes Twig integration straightforward if a developer wants it.
The critical mechanism is in DGZ_Controller::display(), which captures all view output using
PHP's output buffering:

  ob_start();
  call_user_func_array([$this, $method], $inputParameters);
  $contentHtml = trim(ob_get_clean());

The pipeline does not care how that HTML was produced. Whether a view's show() method uses
inline PHP or calls echo $twig->render('template.twig', $data), the result is identical — a
string of HTML — and the rest of the pipeline (layout, SEO, flash messages, asset injection)
continues unchanged.

Steps to add Twig:

  1. Install the package:

       composer require twig/twig

  2. Bind a Twig\Environment singleton in bootstrap/app.php (or bootstrap/custom_helpers.php):

       $loader = new \Twig\Loader\FilesystemLoader(base_path('templates'));
       $twig   = new \Twig\Environment($loader, ['autoescape' => 'html']);
       $container->singleton(\Twig\Environment::class, fn () => $twig);

  3. Create a TwigView base class:

       namespace Dorguzen\Core;

       class TwigView extends DGZ_HtmlView
       {
           protected \Twig\Environment $twig;

           public function __construct(DGZ_Controller $controller)
           {
               parent::__construct($controller);
               $this->twig = container(\Twig\Environment::class);
           }

           protected function render(string $template, array $data = []): void
           {
               echo $this->twig->render($template, $data);
           }
       }

  4. Create your Twig templates in the templates/ directory:

       templates/
       └── products/
           └── index.twig

  5. In any view's show() method, replace inline PHP with a render() call:

       class ProductsView extends TwigView
       {
           public function show(): void
           {
               $this->render('products/index.twig', [
                   'products' => $this->controller->getProducts(),
                   'title'    => $this->controller->getPageTitle(),
               ]);
           }
       }

  That is the entire integration for views. The router, controller, layout, SEO pipeline,
  flash messages, and asset injection all continue to work exactly as before.

  To Twigify layouts as well (so you can use Twig's {% extends %} across the full page
  shell), create a TwigLayout extending DGZ_Layout that renders a .twig layout template
  in its display() method, passing $this->content, $this->notices, $this->metadata etc.
  as Twig variables. Then point configs/app.php at that layout class instead of the
  default seoMasterLayout.

  Note: Twig's native template inheritance ({% extends %} / {% block %}) only works within
  Twig's own template graph. If you Twigify views but keep the PHP layout class, you cannot
  use {% extends %} to inherit from the outer shell — you would use Twig's {% include %} and
  macros within views, while the PHP layout class remains the outer shell. Both hybrid and
  full-Twig approaches are valid and require no framework modifications.




——————————————————————
	PACKAGE MANAGEMENT
——————————————————————

  Almost every programming language has a way to manage assets, also known as packages or libraries. This also means managing their versions for compatibility reasons.




——————————————————————
	OOP
——————————————————————

-Object Oriented Programming (OOP) refers 
  to the ability of a programming language 
  to handle the building blocks of an 
  application as objects and properties.
-This offers a much more organised, 
  readable, testable, and scalable 
  approaches to building software 
  applications. These benefits it offers and 
  more, will be evident as the OOP topic is 
  broken down in detail.


    -DEPENDENCY INJECTION AND THE DI CONTAINER
    -Tips and best-practices on how to use the DIC container 
      -Register models as singletons at bootstrap
    



-DEPENDENCY INJECTION AND THE DI CONTAINER

  -NEVER resove classes from a view.
  -Use only one instance of a class in a controller. Do so by having the dependency class injected 
    via the constructor at initialisation.

    -You can resolve classes from the DI container from anywhere in your project in any of the following ways:

      use Dorguzen\Models\Logs;

      // write something to the logs table
      $container = container();
      $container->get(Logs::class)->log(...);

    or

      $container = container();
      $logs = $container->get(Logs::class);
      $logs->log(...);

    or 

      $logs = container(Logs::class);
      $logs->log(...);

    or 

      $data = container(MyObject::class)->methodOnMyObject();

      Hereby chaining a call on container() by directly calling a method on the resolved class.
      This works because container() returns the resolved object, hence you can directly chain 
      calls on it.






  Tips and best-practices on how to use the DIC container 
    ---------------------------------
    


✅ Register models as singletons at bootstrap
-----------------------

Before anything else, in bootstrap\app.php, register an object with the container as a singleton  like so:

//------------------------------------------------------------------------
//  REGISTER ESSENTIAL CLASSES WITH THE CONTAINER BEFORE ANYTHING ELSE
//------------------------------------------------------------------------
$container->singleton(Users::class, fn($c) => new Users($c->get(Config::class)));
$container->singleton(Logs::class, fn($c) => new Logs($c->get(Config::class))); 
$container->singleton(News::class, fn($c) => new News($c->get(Config::class))); 
$container->singleton(Subscribers::class, fn($c) => new Subscribers($c->get(Config::class))); 
//------------------------------------------------------------------------

Alternatively, You can automate this by scanning the models folder.

After this, every time you resolve a class from the container, like so:

    container(Users::class) 
    
The returned object will ALWAYS be one and the same instance. The result is:

✅ No repeated reflection.
✅ No repeated constructor work.
✅ No repeated config loads.
✅ No repeated error_log spam.

This alone will drop page load times from over 30 seconds to < 1 second.









//—————————
FFIIEENA 
//----------————
——————————————————————
	FILE MANAGEMENT
——————————————————————

-The managing of files is a very important part of every software application. This topic will demonstrate how your programming language is able to manage files (create, view, modify, delete, and transmit them through local/remote networks).

-This section also deals with the ability of 
  your application code to reference local 
  and remote assets and use them as if they 
  were local to it.
-A notable example is the inclusion of other 
  files that contain code that we want to 
  include or import into the current file and 
  use. This helps facilitate code re-use, 
  which is known to be a good practice.
-The file being referenced can be either 
  local in the same directory, or a different 
  directory, in a different locally installed 
  software package or software, or remote 
  on a whole different domain.
-While some programming languages let 
  you reference external code into the 
  current code using the 'include' keyword, 
  like PHP, others use the keyword like Java, 
  Python and Golang, but the idea behind 
  them all is the same.





——————————————————————
	FORMS AND EMAIL
——————————————————————

            -How to secure your forms against CSRF attacks
            -How to submit non-conventional PUT, PATCH, & DELETE requests
            -How to send a test AJAX request from the CLI with a CSRF token 
            -How to make an API request
            -Example of creating a form with the DGZ_Form class
                -Create a select form field

            -Jet forms, Dorguzen's re-usable forms
                -Introduction
                -Purpose of JetForms
                -JetForms Architecture Overview
                -Lifecycle of a DGZ Jet Form
                    -i) Create the Jet form 
                    -ii) Register the Jet form
                    -iii) Display the Jet form
                -Using a jet form in a controller
                -Passing more than one Jet form to a view
                -How Jet forms are validated
                -Adding extra hidden fields on the fly
                -Prepopulating form with sample data
                -How to make non POST or GET form requests 
                -Optional business logic

            -Sending Emails Dorguzen
                -SMPT Configuration (.env)
                -Public Send Methods
                -Template System (renderEmail)
                -File Locations
                -Customising Email Templates




    
This demonstrates how your programming language allows you to manage web forms and user-submitted input.
For validation of form submissions and all types of requests, see the Security section for documentation on 
using the DGZ_Validator class. 


    How to secure your forms against CSRF attacks
    ----------------------------------------------
  To secure your forms with Cross-Site Request Forgery (CSRF) protection, all you have to do is add a hidden 
  field. The name has to be '_csrf_token' and its value must be a Dorguzen-generated csrf token, and DGZ has 
  a global function to do this for you easily, the function is getCsrfToken(). Add the hidden field to your 
  form like this:

    <input type="hidden" name="_csrf_token" value="<?=getCsrfToken()?>">




  Alternatively, if you are displaying the form from your controller method without generating a template 
  (view) file, you can reference that same function on the controller's request properety like so: #
    
    $token = $this->request->getCsrfToken();

  Here is an example:

    class TestController extends DGZ_Controller
    {
        ...

        public function form()
        {
            $token = $this->request->getCsrfToken();
            $app = $this->config->getFileRootPath();
            echo "
                <form method='POST' action='{$app}test/submit'>
                    <input type='hidden' name='_csrf_token' value='{$token}'>
                    <input type='text' name='message' placeholder='Type something'>
                    <button type='submit'>Send</button>
                </form>
            ";
        }

        public function submit()
        {
            echo "✅ CSRF validation passed — request accepted!";
        }
    }

    But because the helper function getCsrfToken() exists, you can just use it directly like this:

      $token = getCsrfToken(); 

    or just call it inline, within the value attribute of the form's _csrf_token field like this: 

        <input type="hidden" name="_csrf_token" value="<?=getCsrfToken()?>">

    With that in place, you do not have to do anything else, as Dorguzen will validate the token 
    for you on the other end when it's submitted. The submission will error if the form is missing 
    the token field, and the clear message will be logged to inform you the application owner of what 
    file and line the error occured at.

    If you are using Dorguzen's DGZ_Form class, to create your form-which is the recommended way to 
    create forms in the DGZ framework, you do not have to worry about adding that csrf input field in 
    the form. All that is handled for you behind the scenes to you can just focus on creating your 
    form input fields and handling the submission in your controllers. 
    
    -In configs/Config.php, there is an array 'csrf_except' in which you can define certain request 
    paths that you want the csrf validation to be ignored. So, decide what routes you want to make
    CSRF validation exemptions for and add them to that array. 
    
      'csrf_except' => [
					'/api/',
      ]
    
    Currently, as you can see CSRF will not be applied to any route that matches 'api' in the browser.
    For all other routes, Dorguzen is currently applying CSRF validation for the following request 
    methods:
        POST, 
        PATCH,
        PUT, 
        DELETE
    
    
    Why Dorguzen is exampting API requests is because APIs already have their own validation going on, 
    and that is by using a JSON Web Tokens (JWT), which should suffice.  



        How to submit non-conventional PUT, PATCH, & DELETE requests
        --------------------------------------------------------
  While backend systems running in languages like PHP understand and can handle PUT, PATCH, and DELETE 
  requests, standard HTML forms (<form>) only support GET and POST request methods. 
  To simulate PUT, PATCH, or DELETE from a form, you'll need this small trick 
  to deceive the browser into thinking you are submitting a POST request, while you actually use a
  hidden field with some value in it which you can read on the backend after submitting the form, to 
  determine if the intension of the request was to make a PUT, PATCH, or DELETE request.

        <form action="/users/1" method="POST"> 
          <input type="hidden" name="_csrf_token" value="<?=getCsrfToken()?>">
          <input type="hidden" name="_method" value="DELETE"> 
          <button type="submit">Delete</button> 
        </form>

  On the server side, when the form is submitted, you can detect request methods in PHP, using the 
  super global $_REQUEST_METHOD. Here is an example of detecting and handling a request as per the 
  method name submitted via that * form's hidden field. This method will correctly detect and handle 
  all HTTP methods, including: 
  
    GET 
    POST 
    PUT 
    PATCH 
    DELETE 
    
  and even others like OPTIONS or HEAD. 
  
    public function method(): string 
    { 
      $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET'); 
      
      // Allow method override (for forms) 
      if ($method === 'POST' && isset($this->post['_method'])) 
      { 
        $method = strtoupper($this->post['_method']); 
      } 
      
      return $method; 
    }

  This is exactly how form request methods are being detected by Dorguzen in the DGZ_Request
  object. So, to detect the method used after a request has been submitted, do it in your 
  controller like so:

    $method = $this->request->method();




      How to send a test AJAX request from the CLI with a CSRF token
      --------------------------------------------------------
    You will find that when you send a test AJAX request from the command line to your Dorguzen 
  application, if it's one of those requests method types for which CSRF validation is enforced,
  you will get an "invalid or missing token" error. This will happen even if you sent through 
  a CSRF token you got from $_SESSION['csrf_token'], or generated by calling getCsrfToken() like 
  so:
  
      curl -X POST http://localhost:8888/camerooncom/test/submit \
      -H "X-CSRF-TOKEN: 3838f4c71de59ac1c16b34bc554c6c886484dfae4e5f8322cf092e30794697dd" \
      -H "Content-Type: application/json" \
      -d '{"foo": "bar"}'

  Here is why that is failing. First of all, let's understand how CSRF systems work. 
  Here is what Dorguzen does:

    A CSRF generator — e.g. getCsrfToken() — that:
      Generates a random token
      Saves it in the session  
      Returns it to embed in forms or headers

    Whenever requests are send eg via forms, its validator grabs the token you sent with the form
    and compares it to the one in the session.

  Here's why your test AJAX call from the CLI is not working:
    As explained above, when you open a browser page (like /test/form), a CSRF token is generated 
      and stored in that browser session.
    But when you run your curl command from the CLI, it's a different session — it doesn't share 
      cookies with your browser, so the server has no session data for that token.
      That's why the backend says “invalid or missing token” even though the token string matches.
      It's not in the same session context.

  Here is how to fix this. When making an API call or simulating an AJAX request from the CLI-and 
  this applies to whether you are using Curl or Postman, you need to send the session cookie as 
  well, so PHP can find the stored token.
  You can get your current session ID by inspecting cookies in your browser's DevTools > under 
  Application, and in Cookies. What you need is the value of the PHPSESSID cookie, which is PHP's 
  session cookie stored on your computer. Its value looks something like this: 
  h9vfjdpa0hevanpgqf1un7nfia. Add that to you CLI AJAX request like this:

      curl -X POST http://localhost:8888/camerooncom/test/submit \
     -H "X-CSRF-TOKEN: 3838f4c71de59ac1c16b34bc554c6c886484dfae4e5f8322cf092e30794697dd" \
     -H "Content-Type: application/json" \
     -H "Cookie: PHPSESSID=h9vfjdpa0hevanpgqf1un7nfia" \
     -d '{"foo": "bar"}'

  Now your CLI requests should work just fine and pass Dorguzen's CSRF validation. 

  Here is an alternative approach for APIs. To allow you to build API routes that don't rely on 
  session-based CSRF (like mobile or SPA clients), Dorguzen skips the CSRF check for routes starting 
  with /api/. This happens in the Dorguzen middleware already in place, and it skips API requests 
  because of the 'csrf_except' config key entry in configs/Config.php.

  Basically, CSRF protection is only enforced for web routes, not stateless API endpoints. That is 
  how it is meant to be. API routes already have their own separate validation, and that is by using 
  JSON Web Tokens (JWT).




      How to make an API request
      -----------------------------
    The endpoint to API requests looks like this:

        http://localhost:8888/camerooncom/api/someController-methodName

    Dorguzen ships with CSRF validation turned off for API requests, but you need to submit your 
    JWT tokens along in the request. This should be done except for login requests where tokens 
    will be created for you, and sent back with the response. Here is an example of an API call 
    made from the CLI using httpie to the refresh token endpoint: 

        http POST http://localhost:8888/camerooncom/api/auth-refresh_token \
        "Authorization:Bearer eyJ0eXAiOi7N345YNo8bM..."

    Here is the syntax of another API request made to grab all the favourite products by a user:
    
        http POST http://localhost:8888/camerooncom/api/ad-favourites "Authorization:Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1N..." Content-Type:application/json <<< '{"caller-origin":"api", "user_id":"1"}'
        
        OR:
        
        http POST http://localhost:8888/camerooncom/api/ad-favourites \
        "Authorization:Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..." \
        caller-origin=api user_id:=1




        Example of creating a form with the DGZ_Form class
        --------------------------------------------------------
 The following example is from a PHP script that creates a co-manager for an existing shop. 
 Just focus on the syntax of the DGZ_Form in use.

      <?php
      $form = new DGZ_Form();
      $shopId = $shopData['shop_id'];
      $shopOwnerId = $shopData['gold_members_users_id'];

      
      $form::open('createCoManager', $this->controller->config->getFileRootPath().'shop/saveCoManager', 'post');

      echo '<div class="form-group">';
      $form::label('coManagerId', 'Enter the user ID <small>(A user can get their ID from the User Dashboard when they are logged in )</small> <span id="shopNameInfo" style="color:red;">*</span>');
      $form::input(
              'coManagerId',
              'text',
              ['class' => 'form-control'],
              isset($_GET['cm'])?htmlentities($_GET['cm'], ENT_COMPAT, 'UTF-8'):'');
      echo '</div>';

      $form::hidden('shop_id', $shopId);
      $form::hidden('shop_owner_id', $shopOwnerId);
      if (isset($_GET['cm'])) {
          $form::hidden('old_comanager_id', htmlentities($_GET['cm'], ENT_COMPAT, 'UTF-8'));
      }

      echo '<div class="form-group">';
      $form::submit('button', 'Cancel', ['class' => 'btn btn-warning btn-sm', 'href' => $this->controller->config->getFileRootPath().'shop/manage-shop?userId='.$shopOwnerId]);
      $form::submit('submit', isset($_GET['cm'])?'Update Manager':'Create Manager', ['class' => 'btn btn-primary btn-sm ml-3']);
      echo '</div>';

      $form::close(); ?>

   As you can see, here are the key points to note:
      -You have to first of all instantiate the form: 

          $form = new DGZ_Form();

      -The first thing you must do after instantiating the DGZ_Form class is to open the form 
        using its open() method. For example:
  
          $form::open('createCoManager', $this->controller->config->getFileRootPath().'shop/saveCoManager', 'post');

      -Build form labels and their fields using label() and input(), respectively eg:

          $form::label(...)
          $form::input(...)

      -You create a hidden input field like this:

          $form::hidden(...)

      -Add any other fields you wish to add to the form. See the class for all the fields supported.

      -Finally, you must call close() to close the syntax of the form element.

          $form::close(); ?>

    That is it. The great thing about using the DGZ Form class is that, it already handles CSRF protection 
    for you. If you arwe curious, that happens when you start the form, like this:
      
        DGZ_Form::open().



      Create a select form field
      ---------------------------
      The DGZ_Form::select() method supports the following:

        -allows nested optgroups
        -supports preSelected values
        -escapes values (safe HTML)
        -supports both single & multi-select 

      The method accepts the following arguments:

        @param mixed $selectName the name of the select field. This will also be used as its ID
        @param mixed $data an associative array of data to display in the select field in 'key => value' pairs 
            where the keys will be the option values, & values the option text shown to the user
        @param mixed $preSelected this will contain a numerically-indexed, single-level array of 
            strings matching the value(s) that you want preselected
        @param mixed $multipleSelect whether you want the field to be a multi-select field or not
        @param mixed $attributes any attributes you want applied to the select tag
        @return string containing the created select field

    To use it, pass it an associative array as the $data argument. Here is an example: 

        $myform = DGZ_Form::select(
                'gender', 
                [
                  '1' => 'Male', 
                  '2' => 'Female'
                ]
              );

      OR

    if the data is a multi-dimensional array, it automatically becomes an optgroup.

        $myform = DGZ_Form::select(
                'gender', 
                [
                  'Gender' => 
                      [
                        '1' => 'Male', 
                        '2' => 'Female'
                      ]
                ]
              );

    You can create a standard select field with optgroups like this:

        DGZ_Form::select(
          'category',
          [
              'Phones' => [
                  'iphone' => 'Apple iPhone',
                  'samsung' => 'Samsung Galaxy',
              ],
              'Laptops' => [
                  'macbook' => 'MacBook Pro',
                  'lenovo' => 'Lenovo Thinkpad',
              ],
              'other' => 'Miscellaneous'
          ],
          ['iphone'], // pre-selected
        );


    Here's a full example:

          $form = new DGZ_Form();

          $form::open('chooseCategory', $this->controller->config->getFileRootPath().'shop/saveCategory', 'post');

          $form::select(
            'category',
            [
                'Phones' => [
                    'iphone' => 'Apple iPhone',
                    'samsung' => 'Samsung Galaxy',
                ],
                'Laptops' => [
                    'macbook' => 'MacBook Pro',
                    'lenovo' => 'Lenovo Thinkpad',
                ],
                'other' => 'Miscellaneous'
            ],
            ['iphone'], // pre-selected
          );

          $form::close(); ?>






          Jet forms, Dorguzen's re-usable forms
          --------------------------------------
            -Introduction
            -Purpose of JetForms
            -JetForms Architecture Overview
            -Lifecycle of a DGZ Jet Form
                -i) Create the Jet form 
                -ii) Register the Jet form
                -iii) Display the Jet form
            -Using a jet form in a controller
            -Passing more than one Jet form to a view
            -How Jet forms are validated
            -Adding extra hidden fields on the fly
            -Prepopulating form with sample data
            -How to make on-POST / non-GET Form Requests (PUT, PATCH, DELETE) 
            -Optional business logic


                  
                
                
   


  Introduction
  ------------
  Forms are like the portal into your application from the outside world. Any dynamic software 
application or website needs a form, whether it is to get feedback or input from users to 
provide data to the application to respond to. With that comes the task to collect the data, 
paying attention to the data type needed, sending it to the right part of the application, also 
known as the handler, and then knowing which form was submitted, and from what part of the 
application so that feedback can be returned to the user in the form of an acknowledgement of 
receipt, or feedback if there were errors with the submission. With this transaction also comes 
security concerns, as malicious data can be fed into you application. Validation and sanitization 
of the submitted data is very crucial. 
  The DGZ_Form provides you a great interface for building any type of form, but it does not meet 
all those needs. For an application that needs many forms, especially similar forms to use in 
different areas of the application, Dorguzen has the solution via Jet forms. The principle behind 
it is to let you create one form and use it everywhere within the application.    
  This guide explains:
    -How the JetForms system is structured.
    -What class component (JetForms, JetFormsRegistry, your reusable form classes) is responsible for.
    -The lifecycle of a form from creation → population → validation → persistence.
    -How developers should use forms in DGZ (with a ContactForm as an example).                



    Purpose of JetForms
    --------------------
  JetForms gives DGZ a reusable, object-oriented, framework-level forms system, similar to Symfony, 
Laravel Form Requests, and Falkon, only with less abstraction and complexity. The interface is meant 
to be so easy for any developer to use to create and re-use large and complex forms with ease, without 
worrying too much about syntax and security. There should be no manual typing of HTML code to create 
the form fields, and ensuring they are syntactically correct for the backend handler. So, ease of 
use, speed, re-usabolity and out-of-the-box security are the buzzwords at play. Jet forms was the name 
born from the solution to all of that.  


It solves these problems:
  -Avoid manually constructing forms in controllers/views
  -Avoid rewriting the same validation rules
  -Automatically repopulate form fields after submission
  -Provide a clean API for defining fields
  -Provide a standard place to run validation
  -All while allow the same form to implement custom logic when used in different places 




  -JetForms Architecture Overview
  ----------------------------------
    There are three main components:

a. JetForms (base class)
  Every reusable form extends this.
  It handles:
    -Defining form fields
    -Populating submitted data
    -Providing validation rules
    -Running validation
    -Providing sanitized input access
    -Returning errors
    -There is room for Hooks like afterValidate() or persist() if the developer wants to add functionality

b. JetFormsRegistry
  A global registry that:
    -Stores all form instances
    -Allows DGZ (especially the Router) to retrieve forms anytime
    -Makes it possible to use forms across controllers, middleware, and views
    -Think of it like a “container” that keeps track of active form objects.

c. Reusable forms inside src/forms/
  Example: ContactForm
  These forms:
    -Extend JetForms
    -Define their own fields
    -Define their own validation rules
    -May be extended by being made to define optional behaviour (send email, save to DB, etc.)
    -Every reusable form must have the following members:

        public string $name = 'contactForm';
        public string $handler = 'data/test-contact-form';
        public string $method = 'contactForm';
        public string $redirectBack = 'data/privacy';
        public array $rules = [];
        public array $messages = [];
        protected function renderFields()


  The $name property is the key used to register the form in Dorguzen. More on this below.
  The $handler is the path string of the script to handle the form submission-usually a 
    controller and method. This must be a legal (existing) route.
  The $method property is the HTTP method to be used in submitting the form. It will be used 
      in the 'action' attribute of the form. 
  The $redirectBack is the opposite routing of $handler. It should be a legal (existing) route 
    that displays the form. This will be used by the system to know where the form was submitted 
    from so it can redisplay any feedback-e.g. submission #acknowledgement or errors after 
    validation.
  The $rules property is the same as all forms that use DGZ_Form. It contains an associative array 
    of field names, and the validation rules that will guide the validation. 
  The $messages property is also the same as all forms that use DGZ_Form. It contains an associative 
    array of the defined rules as the keys, and the messages you would like to be shown to the user 
    if the validation of that field fails. 
  The method renderFields() is where you will create the actual form fields for the form class. To 
    do so, you will use the form helper methods of DGZ_Form like so:

      public function renderFields() {
        self::label('name', 'Your name');
        self::input('name', 'text', ['class' => 'form-control'], $this->data['name'] ?? null);

        self::label('email', 'Email');
        self::input('email', 'email', ['class' => 'form-control'], $this->data['email'] ?? null);

        self::submit('submit', 'Send', ['class' => 'btn btn-primary']);
      }

  Then to display the form in any view, you just have to instantiate the specific for, set any parameters 
  using the available setter methods, and then call render().

      <?php
        $form = new \src\forms\ContactForm();
        $form->setHandler('seo/test-contact-form-from-module'); 
        $form->setRedirectBack('seo');
        $form->render(['class'=>'my-form']); 
      ?>

  The render() method is a method on the JetForms class which wraps the renderFields() method of the form 
  you are creating while generating the full form including opening and closing tags, with any parameters 
  you have set for it. In this example above, we are creating a form directly in a view file, but you can 
  also create the form on the fly in a controller, and pass it to a view. We will see an example shortly.   



  -Lifecycle of a DGZ Jet Form
  -------------------------------
   Below is the step-by-step process from request → registry → validation → view rendering. This will 
  another example of creating a form in a view file. Let's go in steps: 

    -i) Create the Jet form 
    -ii) Register the form 
    -iii) Display the form 
    
          -i) Create the Jet form 
          ------------------------
    So assuming we already have a 
  Jet form named ContactForm, and it lived in src/forms/. Here is the full example code: 

      namespace Dorguzen\Forms;

      class ContactForm extends JetForms
      {
          public string $name = 'contact_form';


          /** 
           * handler string. The valid route to the form handler, 
           * usually a controller to process the form. 
           * This must be a valid route
           */
          public string $handler = 'data/test-contact-form'; // optional


          /** handler string. HTTP method to use to submit the form */
          public string $method = 'POST'; 


          /** 
           * redirectBack string. Path to send the user back to if validation fails. 
           * This must be the valid route to the view that displays the form. 
           * This is how the application knows which view the form was submitted from. 
           */
          public string $redirectBack = 'feedback/contact'; 


          protected array $rules = [
              'name' => 'required|min:3|max:100',
              'email' => 'required|email',
              'message' => 'required|min:10',
              'category' => 'nullable|in:support,sales,other'
          ];


          protected array $messages = [
              'name.required' => 'Please tell us your name.',
              'email.email' => 'Please provide a valid email address.',
              'message.min:10' => 'Message must be at least 10 characters.'
          ];

          

          protected function renderFields(): void
          {
              // Use DGZ_Form helpers to build the form fields
              self::label('name', 'Your name'); 
              self::input('name', 'text', ['class' => 'form-control'], $this->data['name'] ?? null);

              self::label('email', 'Email');
              self::input('email', 'email', ['class' => 'form-control'], $this->data['email'] ?? null);

              self::label('message', 'Message');
              self::input('message', 'textarea', ['class' => 'form-control'], $this->data['message'] ?? null);

              self::label('category', 'Category');
              self::select('category', [
                  'support' => 'Support',
                  'sales' => 'Sales',
                  'other' => 'Other'
              ], [$this->data['category'] ?? ''], false, ['class' => 'form-select col-md-12']);

              echo '<br>';

              self::submit('submit', 'Send', ['class' => 'btn btn-primary']);
          }




          -ii) Register the Jet form 
          ------------------------
    For a Jet form to work, we have to register it with the src/forms/JetFormsRegistry class. This class 
  keeps track of all re-usable form classes in the system. To do so, visit bootstrap/app.php 
  and find the section where Jet forms are registered. Then add this to the code:

      $container->get(JetFormsRegistry::class)->register('contact_form', ContactForm::class);

  Don't forget to use the Jet form class at the top of the file:

      ...
      use Dorguzen\Forms\ContactForm;

  This registration step provides an extra level of security. This is in the sense that, the only forms processed  
  on submission will be forms that are known by the system.



        -iii) Display the form 
        -----------------------
    This is so simple, becaue it can be done in as little as two lines. In the view where you wish to create 
  the form, do it like this:

        $form = new \src\forms\ContactForm();
        $form->render(['class'=>'my-form']);

  Tip: the route of the view where you are displaying this form should match the route you specified as the  
  value of the redirectBack property in the form. This just tells Dorguzen where to send feedback to the user 
  after the form submission, or errors in case validation failed. 



    Using a jet form in a controller
    ---------------------------------
  Most often you would create a Jet form on the fly within a controller and pass it to the view. This is how; 
  say you are in the contact() method of a controller, FeedbackController. Before displaying the views/contact.php 
  create the form and send it through as follows: 
    
      public function contact()
      {
          $form = new \src\forms\ContactForm();
          
          $view = Dorguzen\Core\DGZ_View::getView('contact', $this, 'html');
          $this->setPageTitle('Our contact page');
          $view->show(['form' => $form]);
      }

  Then in the view file (in this case 'views/contact'), retrieve the form from the data array passed through and 
  render it: 

      namespace views;

      class contact extends Dorguzen\Core\DGZ_HtmlView
      {
          ...
          function show($data)
          {
              $form = $data['form'];
              $form->render(['class'=>'contact-form']);
          }
      }




    Passing more than one Jet form to a view
    ---------------------------------
  You can pass more than one Jet form from the controller to the view. This is how; 
  say you are in the contact() method of a controller, FeedbackController. Before displaying the views/contact.php 
  create the forms and send them through as follows: 
    
      public function contact()
      {
          // Passing two forms to a view file
          $data = [
              'name' => 'Donald',
              'email' => 'don@google.com',
              'message' => 'test message',
              'category' => 'sales'
          ];
          $form = new ContactForm();
          $form->addHiddenField('_method', 'PUT'); 
          $form->fill($data);

          // another form
          $form2 = new AnotherForm();
          
          $view = DGZ_View::getView('privacy', $this, 'html');
          $this->setPageTitle('privacy policy');
          $view->show(['form' => $form, 'form2' => $form2]);
      }

  Then in the view file (in this case 'views/contact'), retrieve the form from the data array passed through and 
  render it: 

      namespace views;

      class contact extends Dorguzen\Core\DGZ_HtmlView
      {
          ...
          function show($data)
          {
              $form = $data['form'];
              $form->render(['class'=>'my-form']);
              echo "<br>";

              // the second form
              $form2 = $data['form2'];
              $form2->render();
          }
      }



    How Jet forms are validated 
    --------------------------------------
    The great thing about Jet forms is that they are validated automatically, so we'd say its developers hands-off. 
    The magic happens in middleware/FormValidationMiddleware.php. Here is a breakdown of what happens once the 
    Jet form submission is intercepted. 
  
    First, the FormValidationMiddleware detects a submitted form. It does this by checking for the existence of 
  an input named '_form_name', which is in the hidden input field in the form containing the name of the given form.     
  It then resolves the form by that name from FormRegistry, then proceeds to do the following:

    -fills the form class with the request data,
    -validates the form based on its own defined rules,
    -throws a ValidationException if validation fails,
    -the DGZ router catches the ValidationException & handles it
       then sets SESSION['old_input'] & SESSION['validation_errors']
       (which is useful in re-populating form in the next steps)
    -if validation passes, it sets on SESSION['old_input'], and  
       then lets the controller handle further processing.

  Dorguzen's middlewares are ran by priority, so the middleware CSRF validation checks have priority 1, so that will 
  always be checked for first, before detecting if the request is a Jet form. 
  However, you can adjust ordering as you see fit.

  If validation fails, a ValidationException() exception is thrown, which the router will catch.
  After catching this ValidationException, the router sets a session flash message & redirects to referer. 
  That is how a wrongly submitted Jet form is redisplayed with errors for the user to fix.



    Adding extra hidden fields on the fly
    --------------------------------------
  Use the addHiddenField() method of the JetForms class. It takes two arguments, the name of the hidden field, and 
  its value.

      $form = new \src\forms\ContactForm();
      $form->addHiddenField('_method', 'PUT'); 



    Pre-populating a form with sample data 
    ------------------------------------
    There will be times when you want to display a form and have some sample/dummy data populating the fields, ready 
    to test a submission with, or it could be just some default data you want to be submitted fror a field if nothing 
    is entered by the user. Just pass an associative array of keys and values where the keys exactly match the names 
    of your form fields. Here is an example of creating a form with some data for pre-populating the form:

      $data = [
          'name' => 'Donald',
          'email' => 'don@google.com',
          'message' => 'test message',
          'category' => 'sales'
        ];
        $form = new \src\forms\ContactForm();
        $form->addHiddenField('_method', 'PUT'); 
        $form->fill($data);
        
        $view = Dorguzen\Core\DGZ_View::getView('contact', $this, 'html');
        $this->setPageTitle('Our contact page');
        $view->show(['form' => $form]);



    How to make on-POST / non-GET Form Requests (PUT, PATCH, DELETE)
    ------------------------------------------------
  HTML forms only support two HTTP methods natively:

    GET
    POST

This is not a Dorguzen limitation — it is an actual browser limitation.
Browsers do not send PUT, PATCH, or DELETE requests through normal <form> submissions.

To send those methods, you normally need to use JavaScript techniques such as:
  AJAX requests (XMLHttpRequest)
  fetch() requests
  A JavaScript framework (Vue, React, etc.)

However, server-side frameworks have long used a simple trick to simulate PUT/PATCH/DELETE 
even when using standard HTML forms. Dorguzen supports the exact same trick.
This is the Hidden _method Field Trick
  If you want a form to behave like a PUT, PATCH, or DELETE request, the form should still submit using:

    <form method="POST">

…but inside the form, include a hidden field called _method telling Dorguzen which actual method you intended:

    <input type="hidden" name="_method" value="PUT">

Your controller or middleware can then treat the request as if it were truly a PUT request.
This pattern is used by frameworks like Laravel, Symfony, Rails, and now Dorguzen JetForms.
How to Add the _method Hidden Field in JetForms

If you are building the form using JetForm in a view file, or controller on the fly, simply call:

    $form->addHiddenField('_method', 'PUT');

Or PATCH / DELETE, depending on what you want.

Example (in a view):

    $form = new \src\forms\EditPostForm();
    $form->addHiddenField('_method', 'PATCH');
    $form->render();

Example (in a controller):

    $editForm = new EditPostForm();
    $editForm->addHiddenField('_method', 'DELETE');

    $view = Dorguzen\Core\DGZ_View::getView('edit_post', $this, 'html');
    $view->show(['form' => $editForm]);

You then need to check for the existence of that hidden _method field in the backend and us it to 
do what you need to do. e.g.

    $method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

If _method exists, treat it as the request method instead of POST.

Typical use-cases for such request methods include:
    -when you need to perform REST-style actions without JavaScript, for example:
    -update a record → use _method = PATCH or _method = PUT
    -deleting a record → use _method = DELETE

If you really prefer true PUT/PATCH/DELETE HTTP requests, then you must use JavaScript (AJAX / fetch), 
because the browser will not generate these natively.




  How JetForms are validated 
  --------------------------
  The validation is done in middleware upon submission. So when the request finally gets to the handler, you need 
  no longer worry about validation. You can then just do whatever else you wish to do, like persist the data to 
  the DB.
  The actual validation process is done like any other form using the DGZ_Validator. The submitted data, 
  together with the validation rules and custom validation messages of the given form are passed to the validate() 
  method, and another method; passes() returns true or false depending on if there were errors or not.   
      




  Optional business logic 
  ------------------------
  You can optionally expand what the FormValidationMiddleware code does. For example; instead of letting the request
to be escalated up the stack till it gets to the target handler (controller), you can choose to perform a task right  
there after the passed validation and do something like logging, send off an email, or even persist the data to DB. 
To give you a random hint; you could add a method or two to the Jet form (in this case ContactForm) class e.g. 

    -afterValidate() 
        this will contain code that can be called to run after the validation
    -persist()
        this can contain code to save the data to the DB

If you decide to go this route, you must add code to call these extra methods of the form class from within 
middleware/globalMiddleware/FormValidationMiddleware's handle() method. Do it below, towards the end, after validation passes, 
but before it returns true.







——————————————————————————————————————————————————————
    SENDING EMAILS IN DORGUZEN (DGZ_Messenger)
——————————————————————————————————————————————————————
        -Overview
        -Testing Emails locally with Mail Trap
        -1. SMTP CONFIGURATION (.env)
            -For local testing with MailTrap
            -⚠️ Before deploying to production — switching to your live mail provider
        -2. PUBLIC SEND METHODS
        -3. RENDERING EMAILS (renderEmail)
        -4. FILE LOCATIONS
        -5. CUSTOMISING EMAIL TEMPLATES

Overview
————————
DGZ ships with a built-in email class, DGZ_Messenger (core/DGZ_Messenger.php), that wraps
PHPMailer. It provides ready-made methods for every transactional email a typical web app
needs — contact forms, account activation, welcome messages, password reset, newsletter sends,
and admin error alerts.

All SMTP credentials live in .env, so you can switch between your production mail provider
and a local mail-catcher (e.g. MailTrap) without touching any code.




      Testing Emails locally with Mail Trap
      -------------------------------------
  Email testing locally is usually a challenge because email service providers often block out emails 
from untrusted servers, which applies to any web applications you may have running on a local server 
on your machine (localhost). To bridge this problem and make developers focus on the email design and 
the working of your application, Dorguzen makes it easy for you to test emails locally with a 
mail-catcher service like Mail Trap. Here are the steps to test your application email sending locally:

      1. Go to https://mailtrap.io and create a free account  
      2. In the dashboard go to Email Testing → Inboxes → click your inbox → SMTP Settings                                                                                      
      3. Select PHPMailer from the integration dropdown — it shows the exact values you need                                                                                    
      4. You'll get something like:   

        Host:       sandbox.smtp.mailtrap.io                                                                                                                                      
        Port:       587                                                                                                                                                           
        Username:   <random string>                               
        Password:   <random string>  

  It might not be straight forward once you log in and visit the dashboard. This is because they might 
change the look of the dashboard or change how any of those links might be accessed. Here is the approach 
in a nutshell. Basically, when it comes to testing local emails on Mail Trap, you need to do that from 
what they refer to as a sandbox. So, once logged in, the first thing you need to do is to look on the 
sidebar for a link to Sandboxes. Click on it, and if you already had a sandbox, you will see in the middle 
section your sandboxes listed under a heading "My Inboxes". You will see nothing if you did not have one 
already, so click on the botton on the top-right that says "Add Sandbox". The instructions are self-
explanatory; they will guide you to create a Sandbox. Once that is done, that sandbox will appear in this 
middle pane whenever you have selected "Sandboxes" on the left sidebar. 

Mail Trap only allows one sanbox for each free user, but that is all you need.

You will see that in this middle pane, the Sandbox name is a link, so click on it to go into that Sandbox. 
Once in it, you will see the middle pane split into two sections, the one on the left will list any emails 
you have received inot your Mail Trap account-this is literally your inbox. This is where you will come to 
check for incoming emails after you have submitted one from your application. The emails will be listed 
here (latest on top). Clicking on any email will display its body and contents on the right.

The URL in your browser should look something like this:

      https://mailtrap.io/inboxes/2257617/messages

On the right, you will see these tabs, which are configurations for your Sandbox: 
    SMTP, Email, API, POP3

The next thing you need to send emails locally is to get your Sandbox's credentials which Dorguzen will 
use to send emails to your Mail Trap Sandbox's inbox. These credentials are four:

    Host, Port, Username and Password.

To get these credentials, whilst in this Sandbox's inbox, look on the right pane and click on the SMTP 
tab. This will reveal all those credentials you need, which should look something like this:

    Credentials

      Host             sandbox.smtp.mailtrap.io
      Port             25, 465, 587 or 2525
      Username         2c63b9f9d3k6c7
      Password         ****380h
      ...

Copy them over to your .env file, comment out the MAIL HOST group of directives meant for email 
sending in the production environment, and create a replication to override that for Mail Trap 
local testing. Here is what your .env file should look like:

      # SMTP / Mail 
      #---------------------------------
      # MAILGUN CREDENTIALS USING SMTP / Mail (PHPMailer)
      # Switch MAIL_HOST/PORT/USERNAME/PASSWORD to MailTrap for local testing.
      #---------------------------------
      # MAIL_HOST=smtp.mailgun.org
      # MAIL_PORT=587
      # MAIL_USERNAME='postmaster@admin.camerooncom.com'
      # MAIL_PASSWORD='postmaster_camcom@'
      # MAIL_ENCRYPTION=tls
      # MAIL_FROM_ADDRESS='noreply@admin.camerooncom.com'
      # MAIL_FROM_NAME='Camerooncom'

      #---------------------------------
      # MAILTRAP TESTING
      #---------------------------------
      MAIL_HOST=sandbox.smtp.mailtrap.io
      MAIL_PORT=587
      MAIL_USERNAME='yourUsernameFromMailTrapIo'
      MAIL_PASSWORD=yourPasswordhere

Next, you just have to trigger email sending from your application to confirm that the email sending 
works. Do something to trigger email, for some, it is the submitting of a contact form your web app's 
contact page (if you know it sends emails). After doing so, go again to mailtrap.io and check the 
inbox of your Sandbox for any in-coming emails, and you should see one.





1. SMTP CONFIGURATION (.env)
—————————————————————————————

Add the following block to your .env (and .env.example):

    MAIL_HOST=smtp.mailgun.org       # your SMTP server
    MAIL_PORT=587                    # 587 (TLS) or 465 (SSL)
    MAIL_USERNAME=postmaster@...     # SMTP username
    MAIL_PASSWORD=your-password      # SMTP password
    MAIL_ENCRYPTION=tls              # tls | ssl
    MAIL_FROM_ADDRESS=noreply@...    # envelope From address
    MAIL_FROM_NAME='Your App'        # envelope From display name

For local testing with MailTrap (https://mailtrap.io):
Test locally, after commenting out the code above which is for the production environment, the 
four lines below are the only lines you need for local MailTrap testing. 

    MAIL_HOST=sandbox.smtp.mailtrap.io
    MAIL_PORT=587
    MAIL_USERNAME=<mailtrap-user>
    MAIL_PASSWORD=<mailtrap-password>
    MAIL_ENCRYPTION=tls

Just swap these four values in .env — no code changes needed. All emails will be
caught by MailTrap's sandbox inbox instead of being delivered to real addresses.

Note: the MAIL_FROM_* values are always used as the envelope sender, regardless of
which SMTP provider is active.


⚠️  BEFORE DEPLOYING TO PRODUCTION — switch back to your live mail provider
————————————————————————————————————————————————————————————————————————————
This is a very common deployment mistake. The .env on your live server must
NOT use Mailtrap credentials — Mailtrap is a sandbox that catches emails and
prevents them from reaching real users. If you deploy with Mailtrap active,
your registration activation emails, password reset emails, and all other
transactional emails will silently disappear into a Mailtrap inbox instead of
reaching your users.

The convention is to keep both blocks in .env and simply comment/uncomment
the right one depending on the environment:

      # SMTP / Mail
      #---------------------------------
      # MAILGUN (production) — uncomment when deploying to live
      #---------------------------------
      MAIL_HOST=smtp.mailgun.org
      MAIL_PORT=587
      MAIL_USERNAME=postmaster@yourdomain.com
      MAIL_PASSWORD=your-mailgun-smtp-password
      MAIL_ENCRYPTION=tls
      MAIL_FROM_ADDRESS=noreply@yourdomain.com
      MAIL_FROM_NAME='Your App Name'

      #---------------------------------
      # MAILTRAP (local testing only) — comment out before deploying
      #---------------------------------
      # MAIL_HOST=sandbox.smtp.mailtrap.io
      # MAIL_PORT=587
      # MAIL_USERNAME=<mailtrap-user>
      # MAIL_PASSWORD=<mailtrap-password>

On your local machine, the Mailgun block is commented out and Mailtrap is
active. On the live server, the Mailtrap block is commented out and Mailgun
(or your chosen provider) is active. One file, both environments, no code
changes ever needed.

Production mail provider checklist:
  ✔  MAIL_HOST — your live SMTP server (e.g. smtp.mailgun.org)
  ✔  MAIL_PORT — 587 (TLS) or 465 (SSL); 25 is often blocked by hosting providers
  ✔  MAIL_USERNAME / MAIL_PASSWORD — credentials from your mail provider dashboard
  ✔  MAIL_ENCRYPTION — tls (recommended) or ssl
  ✔  MAIL_FROM_ADDRESS — a verified sender address on your domain
  ✔  MAIL_TIMEOUT — defaults to 15 seconds; increase if your provider is slow
                    to respond, but do not set it too high or slow SMTP will
                    block your web requests (see ShouldQueue below)

If your activation or reset emails are still not arriving after switching to
the live provider, check the logs table — DGZ_Messenger catches all PHPMailer
failures and logs the exact SMTP error message so you can diagnose quickly.



2. PUBLIC SEND METHODS
————————————————————————
We are looking here at methods that send standard emails that Dorguzen ships with. Some of them 
are in the core/DGZ_Messenger class.
All public methods return true on success and false on failure (PHPMailer exceptions
are caught internally and logged via DGZ_Logger).

    sendContactFormMsgToAdmin($name, $visitorEmail, $phone, $message)
        — Forwards a website contact form submission to the site admin email.
          Also used by sendShopContactMsgToShopOwner() to forward to a shop owner.

    sendNewsletterWelcomeMsg($subscriber_name, $email, $heading, $subject, $message, $image, $imageCaption)
        — Sends the first newsletter to a new subscriber.
          $image and $imageCaption are optional — pass '' to omit the image block.

    sendNewsletterMsg($subscriber_name, $email, $heading, $subject, $message, $image, $imageCaption)
        — Sends a regular newsletter to an existing subscriber.
          Same signature as sendNewsletterWelcomeMsg().

    sendEmailActivationEmail($name, $email, $subject, $message)
        — Account activation email. $message should contain the activation link HTML.

    sendWelcomeEmail($name, $email, $subject, $message)
        — Post-registration welcome email. $message is the body text.

    sendPasswordResetEmail($email, $firstname, $resetCode)
        — Password-reset email. The reset URL is constructed automatically from
          the app's homepage URL + 'auth/reset?em=' + $resetCode.

    sendErrorLogMsgToAdmin($message)
        — Sends an error-log alert to the app admin email. Includes a direct link
          to the admin logs page.

Usage example:

    $messenger = new DGZ_Messenger();
    $sent = $messenger->sendContactFormMsgToAdmin($name, $email, $phone, $message);
    if (!$sent) {
        // handle failure — the error was already logged by DGZ_Logger
    }




3. TEMPLATE SYSTEM (renderEmail)
——————————————————————————————————

Every public send method calls the private renderEmail() method internally.
You never call renderEmail() directly — but understanding how it works lets you
override or extend the email templates.

    private renderEmail(string $view, array $data, string $layout = 'default'): string

How it works:

    1. View resolution — checks two locations in order:
         a) views/emails/{view}.php         <- developer override (your app)
         b) core/email-views/{view}.php     <- framework default

       If a file exists at (a) it is used; otherwise (b) is the fallback.
       This means you can customise any email's content without touching the
       framework core — just create a matching file under views/emails/.

    2. Auto-injected variables — the following are always available in both
       the view file and the layout, without you needing to pass them:
         $appName          from _appName
         $appBusinessName  from _appBusinessName
         $appSlogan        from _appSlogan
         $appURL           from _appURL
         $appYear          current year (date('Y'))
         $heading          email-type heading (defaults to '' if not passed)

    3. Layout wrapping — after the view is rendered into $content, it is
       passed to the layout file at:
         layouts/email/{layout}EmailLayout.php
       The default layout is 'default', which maps to
         layouts/email/defaultEmailLayout.php

    4. The complete HTML string is returned and assigned to PHPMailer->Body.




4. FILE LOCATIONS
———————————————————

    core/DGZ_Messenger.php              The mailer class (public API + renderEmail)
    core/email-views/                   Framework-default email content templates:
        contact-form.php                  — Contact form fields table
        member-email.php                  — Account activation + welcome (shared)
        password-reset.php                — Reset link + CTA button
        newsletter-welcome.php            — Newsletter welcome with optional image
        newsletter.php                    — Regular newsletter with optional image
        error-log.php                     — Error alert with highlighted message block
    views/emails/                       Developer override directory (empty by default).
                                        Drop a file here with the same name as a
                                        core/email-views/ file to override it globally.
    layouts/email/defaultEmailLayout.php  The HTML email wrapper (table-based,
                                        inline + <style> CSS, Outlook-compatible).




5. CUSTOMISING EMAIL TEMPLATES
—————————————————————————————————

To change the content of any email, create an override file in views/emails/:

    views/emails/password-reset.php     <- overrides core/email-views/password-reset.php

Your override file receives the same variables as the framework default (listed in
each file's docblock), plus all the auto-injected variables from section 3 above.
You do not need to output a full HTML document — just the inner body content.
The layout handles the outer HTML, header, accent bar, and footer.

To create a completely different email layout (e.g. a minimal plain-white layout
for transactional receipts):

    1. Create layouts/email/receiptEmailLayout.php
    2. Add a new public send method on DGZ_Messenger that calls:
         $this->renderEmail('my-view', $data, 'receipt')
       (The third argument 'receipt' maps to layouts/email/receiptEmailLayout.php)




      





——————————————————————
	IMAGES
——————————————————————

-This is about how your programming language allows you handle images, not just as files, but in being able to manipulate them in terms of cropping, resizing, retouching and enhancing etc.





-----------------------
INPUT / OUTPUT
-----------------------
    -Requests and responses
      -Example usage of this Request object
      -Retrieve files after they have been uploaded
      -Get values from GET requests
      -Handle and retrieve values from API JSON requests
      -etect a request type or method
      -How DGZ_Request automatically captures data from the $_REQUEST super global
      -Using a class as a singleton
      -Get the IP address of a client
      -Retrieve the Authorization header and extracts a Bearer token
      -Detect if a request is an AJAX request
      -How the CSRF is implemented in DGZ
      -GOLDEN RULES ABOUT REQUEST/RESPONSE LIFECYCLES





  REQUESTS AND RESPONSES
  ----------------------


Example usage of this Request object
    -----------------------------
    -In controllers/services, get submitted values from requests like this:
     //-----------------------------------------------
        $username = $this->request->post('username');
        $email = $this->request->post('email');
        $file = $this->request->file('avatar');


    -Retrieve files after they have been uploaded
     //-----------------------------------------------
        $file = $request->file('avatar');

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $tmpName = $file['tmp_name'];
            $name = basename($file['name']);
            move_uploaded_file($tmpName, __DIR__ . "/uploads/$name");
        }


    -Get values from GET requests like this
     //-----------------------------------------------

        $page = $this->request->get('page', 1);
        $sort = $this->request->get('sort', 'latest');

    -Handle and retrieve values from API JSON requests like this
     //-----------------------------------------------
        $userId = $this->request->json('user_id');
        $productId = $this->request->json('product_id');

        if (!$userId || !$productId) {
            return $this->response->setData([
                'error' => 'Missing required fields'
            ])->setStatus(400)->send();
        }

        // proceed...

        OR

        $data = json_decode($request->getRawInput(), true);
        echo $data['email'];
        echo $data['password'];

        OR (even easier)

        $email = $request->getJson()['email'] ?? null;


    -To detect a request type or method
     //----------------------------------------
        if ($this->request->method() === 'POST') {
            // handle form or API submission
        }

        if ($this->request->isJsonRequest()) {
            // handle JSON body
        }



    -How DGZ_Request automatically captures data from the $_REQUEST super global
     //-----------------------------------------------
      This DGZ_Request object already comes pre-populated with values from 
      PHP's superglobals, and persists them, ready for you to use where you please.
      This block of code below does the exact same thing that many PHP frameworks do
      with a function like Request::createFromGlobals() to capture the values of 
      super globals and load it into its properties, ready for you to use. We do not 
      need that here, and this code block in the constructor of DGZ_Request already 
      does exactly that:

        $this->get = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->files = $_FILES ?? [];
        $this->server = $_SERVER ?? [];
        $this->cookies = $_COOKIE ?? [];

        // Attempt to parse JSON payload if available
        $input = file_get_contents('php://input');
        if ($this->isJson($input)) {
            $this->json = json_decode($input, true);
        } 

      This is great, firstly, because this code is in the contstructor, it will 
      capture and load all the super global data REQUEST data in itself (properties), 
      ready for you to use.

      Secondly, the other thing great DGZ does is; since it is binding it in bootstrap.php file 
      like this:

        $container->set(DGZ_Request::class, function() {
            ... 
            new DGZ_Request()
        }

      it means every controller and service that type-hints DGZ_Request will automatically get 
      the same fully populated instance for that request.
      You don’t need to do anything extra.



    -Using a class as a singleton
     //-----------------------------------------------
     Let's use the example of how we instantiate the
     DGZ_Request class in bootstrap.php to make it globally accessible in the application.
     The class could have been instantiated in any of the following ways:

        -a) Instantiating with a static caching mechanism

            $container->set(DGZ_Request::class, function() {
                static $request;
                if (!$request) {
                    $request = new DGZ_Request();
                }
                return $request;
            });

            OR
        -b) Instantiating with an arrow function (short-hand)

            $container->set(DGZ_Request::class, fn() => new DGZ_Request());

     Both approaches will work. The difference is that the former version adds a static 
     caching mechanism so that:
     
        -the first time the container asks for DGZ_Request, a new one is created.
        -Then every subsequent call will return the same instance (singleton-style behavior).

     Whereas the fn() => new DGZ_Request() version creates a fresh instance each time.
     What is fn()? fn() is PHP’s arrow function syntax, introduced in PHP 7.4.
     It’s a short-hand for creating anonymous functions (closures) that return a single 
     expression. It automatically inherits variables from the parent scope (no need for use(...)).
        Which one is better? The former version (with static) is better in the context of the
     bootstrap.php global configuration file. This is because the HTTP request never changes 
     mid-lifecycle — there’s always one request per application run. So, caching it this way 
     results in the same DGZ_Request instance being reused across controllers, 
     models, etc. This consumes less memory, and there is no redundant parsing.


     -Get the IP address of a client
      //-----------------------------------------------
        $ip = $request->getClientIp();


    -Retrieve the Authorization header and extracts a Bearer token 
     //-----------------------------------------------
     This will be very useful for APIs using JWTs or OAuth2. Here is an example:

        $token = $request->getBearerToken();
        if ($token) {
            // validate JWT, for example
        }


    -Detect if a request is an AJAX request
     //-----------------------------------------------
        if ($request->isAjax()) {
            // Return JSON instead of rendering HTML
        }


    -How the CSRF is implemented in DGZ
     //-----------------------------------------------
     It is done by creating an encrypted token string which is tored in the session
     For it to work, a session is started, ideally in the bootstrap of the app. Here is 
     some more information about how it all works.
        A CSRF token should ideally be created for every user at login and stored in the session just 
     like all the other session variables we store for them (e.g., user_id, email, etc.). We can then 
     delete them when they sign out. To do so, simply call the request->getCsrfToken(). It gets the 
     CSRF token in the user's session if it exists, or creates one and returns its value.  

        -The following four helper methods are created in the request object 
            (Dorguzen\Core\DGZ_Request):

                -createCsrfToken()
                -getCsrfToken()
                -validateCsrfToken(?string $token)
                -getCsrfTokenFromRequest() 

        -This is how to check for a CSRF token & validate it during user requests

            -Form submission

                <form method="POST" action="/account/updateAccount">
                    <input type="hidden" name="_csrf_token" value="<?= $request->getCsrfToken() ?>">
                    <input type="text" name="email">
                    <button type="submit">Save</button>
                </form>

                Notice how the value of the 'csrf_token' field is the result of calling the DGZ global 
                helper function $request->getCsrfToken(). Just getCsrfToken() will still work.

                Note that $request->createCsrfToken() or just createCsrfToken() will work too, as they
                all create a new token if one does not exist, before sending back the token.

                If you sent the request as AJAX, just ensure that you are also generating a token and 
                sending that via the 'X-CSRF-TOKEN' header. Here's an example: 

                    fetch('/user/update-profile', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': generatedCsrfToken
                        },
                        body: JSON.stringify({ name: 'Gustav' })
                    });

                Keep in mind that creating a CSRF to use in your app is flexible, either you choose to 
                generate one and store in a session each time a user logs in, or you can just create 
                one on demand (using createCsrfToken() or getCsrfToken()) whenever you want to use in 
                a form. This means that you can use it to submit forms even when the user is not 
                authenticated. 

            -Handle the form submission in your controller. Notice that the getCsrfTokenFromRequest()
                which is how we grab the CSRF token from the form, is checking for the submitted form 
                field by the name/key of '_csrf_token' both for POST as well as for JSON API requests 
                like so:
                    if (isset($this->post['_csrf_token'])) {
                        ...
                    }

                    $json = $this->getJson();
                    if (isset($json['_csrf_token'])) { ... }
            
                So, after grabbing the user-submitted token (using getCsrfTokenFromRequest()),
                it is validated against what is in the session (using validateCsrfToken($token)).
                Return an error response if it fails validation, or proceed with the request as 
                normal, if ok. 

                    public function updateAccount()
                    {
                        // grab the user-submitted token
                        $token = $this->request->getCsrfTokenFromRequest();

                        // verify the token
                        if (!$this->request->validateCsrfToken($token)) {
                            return $this->response->json(['error' => 'Invalid CSRF token'], 403);
                        }

                        // Proceed if valid
                        $email = $this->request->post('email');
                        // ...
                    }


            -Optionally, you can move the check to your middleware and have the token check & validation
             run for all sensitive requests like: POST, PUT, DELETE, & return an error response if it 
             fails. This is better and saves you having to do CSRF validation in all your controlers.
             DGZ has already ccomplished that for you in the following 2 steps:

                -a) A method; checkCsrfProtection() was created in the Middleware class (in middleware/Middleware.php).
                    The contents of the method are as follows:

                        public function checkCsrfProtection(): bool
                        {
                            /** @var DGZ_Request $request */ /*
                            $request = container(DGZ_Request::class);

                            $method = $request->method();
                            $uri = $request->uri();

                            // Get CSRF exceptions from config
                            $csrfExcepts = $this->config->getConfig()['csrf_except'] ?? [];

                            // Only enforce CSRF on unsafe HTTP methods
                            if (in_array($method, ['POST', 'PUT', 'DELETE'])) {

                                // Skip paths that match exceptions
                                foreach ($csrfExcepts as $exceptPath) {
                                    if (stripos($uri, $exceptPath) !== false) {
                                        return true; // CSRF not required here
                                    }
                                }

                                // Retrieve token using DGZ_Request’s helper
                                $token = $request->getCsrfTokenFromRequest();

                                if (!$request->validateCsrfToken($token)) {
                                    return false;
                                }
                            }

                            return true;
                        }

                -b) Next, in the DGZ_Router class, when the request controller & method are identified, 
                    in the middleware section, just before executing the controller method, we validate 
                    the request for CSRF attacks like so:

                        $middleware = new Middleware($controller, $method);

                        if ($middleware->checkCsrfProtection() === false)
                        {
                            throw new DGZ_Exception(
                                'Not authorized', 
                                DGZ_Exception::PERMISSION_DENIED, 
                                'Invalid or missing CSRF token. If you submitted a form, make sure the form has a hidden field of
                                the name _csrf_token, and its value the result of calling the global getCsrfToken() or getCsrfToken() 
                                function, or if it was an AJAX request, be sure to send the X-CSRF-TOKEN header with its value as the 
                                generated token from getCsrfToken() or getCsrfToken()'
                            );
                        }

                That's it. Again, this way you don’t even have to manually check for csrf inside controllers.


            -How to use CSRF with AJAX requests
              In your web form, just like with any other form as described earlier above, you would 
              have a field like this:

                <input type="hidden" name="_csrf_token" value="<?= $request->getCsrfToken() ?>">

              Remember the token value is from the server (saved in the sessions), so you would use 
              JavaScript to extract that value and insert it into your AJAX code when preparing to 
              send off the request. It should be passed in the headers, as the value of the 
              'X-CSRF-TOKEN'. Here is the example code:
              
                fetch('/user/update-profile', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': generatedCsrfToken
                    },
                    body: JSON.stringify({ name: 'Gustav' })
                });

              In your controller, you would detect if its an AJAX call as shown above, then handle 
              the request as normal-extract, and validate the token, before proceeding. 


            -A good security practice is to re-generate this session token eg at every login
              as we have discussed above. It doesn't have to be at login, but after logging them 
              sounds like a good time to generate it so it is managed together with al the other 
              session values of the user, and when they logout, the whole session including that 
              CSRF token is cleared in one go.  
              To do so, you would add another helper your Request object which you can call from wherever 
              choose to refresh the value of the session CRSF token.
                Anyway, however you choose to do it, the helper method is there on the request object for 
              you to call to recreate, and, or get the token. 

                public function createCsrfToken(): string
                {
                    $token = bin2hex(random_bytes(32));
                    $_SESSION['csrf_token'] = $token;
                    return $token;
                }

              Here is an example of doing that after login the user in, from your controller: 

                public function doLogin()
                {
                    // ... validate credentials then 
                    // create their session vars when they're successfully authenticated.
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['user_email'] = $user->email;

                    // ✅ Create a CSRF token for this new session
                    $csrfToken = $this->request->createCsrfToken();
                    $_SESSION['csrf_token'] = $csrfToken;

                    // Optionally, send it in response for SPA / API apps
                    return $this->response->json([
                        'message' => 'Login successful',
                        'csrf_token' => $csrfToken,
                    ]);
                }


            -Should i use CSRF tokens with API requests? The answer is NO. Here is
             why.

                1. CSRF tokens — for stateful (session-based) web apps       
                    CSRF exists only because of cookies + sessions.
                    When a browser automatically sends cookies with every request, a malicious site can trick t
                    he user's browser into making unwanted requests.

                    The CSRF token, stored in the user's session and embedded in forms, ensures the request originated 
                    from your own site.

                    So:

                    ✅ Use CSRF for your web application, where sessions and forms exist.
                    It's created once per session (e.g., after login) and destroyed on logout — just as explained earlier.

                2. JWT / e.g. Firebase tokens — for stateless (API) systems
                    APIs don't use browser cookies or sessions; they use headers (Authorization: Bearer <token>).
                    Because the browser doesn't automatically attach JWTs, CSRF isn't an issue here.
                    JWTs already guarantee request authenticity, since each API request must explicitly send a 
                    valid signed token.

                    So:

                    ✅ Use JWTs for API authentication.
                    🚫 No need for CSRF protection in your API routes, because no implicit credentials are sent.

                3. Common Hybrid Strategy (Best Practice)
                    Here's how most secure setups work, and you may have guessed it already:

                    Context	                         Authentication	             CSRF Needed?	    Storage
                    --------------------------------------------------------------------------------------------------------
                    Regular web app (HTML forms)	 Sessions (cookies)	         ✅ Yes	           Session
                    API (mobile apps, SPA, etc.)	 JWT / Firebase token        No                 Local storage or header   
                    
            
            
            -How to disable CSRF validation only for your API routes
                -Add an entry to your config file with route or controller names to skip CSRF validation on e.g. 

                    'csrf_except' => [
                        '/api/',
                    ]

                 This entry can also be in the config file of a specific module, and that will work best especially 
                    if your API exists in DGZ as a separate module. Currently, it's not the case, and all api/ calls 
                    are routed via the ApiController. But the plan is to move that to a module later. When it will 
                    be a module, the config file of the module, just like that of all all modules, will live in:

                        configs/apiModuleConfig.php

                -Next, in the validateCsrfToken($token) method of your DGZ_Request class, you check for this 
                    exception array and ignore the validation if a match is found for the current route eg:

                        public function validateCsrfToken($token) 
                        {
                            // 1️⃣ Skip validation for excluded routes
                            $except = $this->config->getConfig()['csrf_except'];
                            $uri = $_SERVER['REQUEST_URI'] ?? '';

                            foreach ($except as $pattern) {
                                if (str_starts_with($uri, $pattern)) {
                                    return true; // skip validation
                                }
                            }

                            // 2️⃣ Otherwise validate normally
                            $stored = $_SESSION['csrf_token'] ?? '';
                            return $stored && hash_equals($stored, (string)$token);
                        }

                -Alternatively, you can take it a step further and do the check more centrally in your 
                    front controller bootstrap.php, or in your middleware. This will look like the 
                    middleware example given above:

                    if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {

                        //do your check to ignore the excluded request types here
                        $except = $this->config->getConfig()['csrf_except'];
                        ...

                        // proceed to validate as normal if current request is not excluded
                        $token = $request->getCsrfTokenFromRequest();
                        if (!$request->validateCsrfToken($token)) {
                            die("Invalid CSRF token");
                        }
                    }


        -TODO: Now i am instantiating DGZ_Request and DGZ_Response classes in the coinstructor of 
            DGZ_Controller, and i am also instantiating it in a singleton fashion in the bootstrap, 
            will that DGZ_Controller instantiation (not a type-hinting) not disrupt the plan of 
            always having only a single instance of DGZ_Request?

           -How many types of super globals are there, 6? have we got them all in DGZ_Request?
           -What's exactly the thing that makes the global helper func work. I guess the fact 
            that they're set in the index.php (front controller), right?

           -Check how to DGZ_Form class handles csrf and make it use the new CSRF feature





Golden rules about request/response lifecycles
------------------------------------------------
Never set a global Request or Response class as a property of a controller.
Controllers must be stateless; requests and responses are lifecycle-bound and must be pulled, never stored.
Explanation 
------------
Controllers must not own request or response objects as properties.
They may ask for them at execution time, but must not store them.

So this is ❌ bad:

    class TestController
    {
        protected DGZ_Request $request;
        protected DGZ_Response $response;
    }


And this is ✅ correct:

    public function meTest()
    {
        $request = request();
        $response = response();
    }

Why? A response is write-once, mutable, and terminal:

  -headers
  -status code
  -output buffer

If a controller holds onto a response: 
  -data leaks across requests
  -headers persist
  -tests explode (you already saw this)

So: never store a response.

2️⃣ The same applies to Request objects

A request is read-heavy, not write-heavy — but it still represents a single HTTP lifecycle.
If you store it on a controller: 
  -the controller becomes stateful
  -request data can leak across:
    tests
    sub-requests
    future dispatches
    mocking becomes painful

So: don't store the request either.

This is what they both represent in application flow:

    -Request → environment snapshot
    -Response → output stream
    -Controller → pure executor

Controllers should:
    -read from the request
    -write to the response
    -own neither

The one allowed exception

    $user = request()->user();
    or 
    $request = container(DGZ_Request::class);

Because:
  -you're pulling, not owning
  -there is no lifecycle coupling
  -there is no persistent state

That is all fine, as long as you don't store it on the controller.
        
    








——————————————————————
	ERROR DEBUGGING AND TESTING
——————————————————————

                -The awesome dgzie() helper function
                -Writing custom logs for your application
                -Creating custom logs with DGZ_Logger 
                  -Dorguzen Logging System — Full Documentation
                -The difference between PHP's error_log() and DGZ_Logger
                -How to write values to console in phpUnit tests

                -PHPUnit Testing
                -Manual testing


-This discusses the tools made available by 
  your programming language to find bugs 
  in your code. It also talks about 
  conventions and best practices for writing 
  reliable, performant and fault-tolerant 
  applications. 
-Performance pitfalls are identified here and 
  work-arounds given. 
-Testing talks about all the tools your programming 
  languages offers you for testing code, including 
  examples of how it is done.



    The awesome dgzie() helper function
    -------------------------------------

  The DGZ framework has a great debugging helper function dgzie(), which is as a wrapper to Symfony's VarDumper(). 
  Here are some notes about it:
    -It is a wrapper around Symfony's VarDumper 
    -It's defined in bootstrap/helpers.php 
    -To use it, just pass it a comma-separated list of things you want to display and halt execution 
      of PHP. These list can e made of variables, literal items, objects, arrays etc. For example:

        dgzie($sql, $params); 

    -The output spots very useful information about the code being viewd, like: 

        -the line the dgzie() function is called on
        -the current request route
        -the state of the current request object
        -and of course, the contents of the items you passed in to be dumped to the screen.
        -As a bonus, it also has good color-coding, and drop-down arrows to reveal nested object and 
          array elements so as to fit nicely on your screen.



      Writing custom logs for your application
      -----------------------------------------
    Beside PHP's built-in error logs which Dorguzen does well to catch and handle elegantly, there are always 
  times when you need to debug a ceretain feature or piece of code and need to be able to log data to efficiently 
  reverse-engineer and pin-point what is going on. For that you can always use the handy PHP error_log() global 
  function anywhere in your code. This will write logs to the 'php_error.log' file of your PHP installation. 
  This file on a MAMP installation on a Mac is located here:

      /Applications/MAMP/logs/php_error.log

  But you may not always have access to this log file, especially if you are working on a shared remote hosting 
  server. For that you have two options:
      -Tell built-in error_log() function to write to a specific log file of your choice. Just pass it the target 
        file location in the third argument (see syntax below).
      -or DGZ has its own logging system that you can use. It's provided by the class 
        Dorguzen\Core\DGZ_Logger.php.

  To use PHP's error_log() to write logs to a custom file, you need to use the 3rd argument. Yes, it has 4 arguments, 
  and the following is an explanation of the 4 arguments:

        error_log(
          string message, 
          int message_type = 0, 
          ?string destination = null, 
          ?string extra_headers = null
        ): bool

      message_type can be either 0, 1, 2, 3, or 4 
        0   means   Send to PHP system logger (default error log)
        1   means   Send email (requires extra_headers)
        2   means   Send to debug connection (not commonly used)
        3   means   ✅ Write message to a file specified in the third argument - the one you need ...
        4   means   Send directly to SAPI logging handler

  So, when you write:

      error_log("DGZ_Request object requested", 3, __DIR__ . '/../storage/logs/dgz_errors.log');

  You are saying:
    3 → write to a custom file
    third argument → the file path to write to

  This is the correct way to log to a specific file using error_log().




      Creating custom logs with DGZ_Logger
      -------------------------------------
    Here is how custom logs in Dorguzen work, in a nutshell. There are three parts:
      -configuration 
      -Dorguzen\Core\DGZ_Logger 
      -models\Logs
    
    First, you have to define the type of logging you 
  want in the Dorguzen\Configs\Config.php file. It basically determines where you prefer logs to be writted to. The setting 
  is defined in the 
  
    'log_driver' 
    
  key. The value can be one of three options:

      'file'
			'db'
			'both'

  But the default value id 'db'. You can always find logs for your application in the logs table, unless you chose 
  'file' as the driver. 
  You can also specify the format using the 

      'log_format'

  key. The value can be one of two options:

      'text' 
      'json'

  But the default is 'text'.

  Essentially, you would call the static method log() of DGZ_Logger and pass it the actual string to be logged. 
    This log() method uses the Logs model to save the logs data to the dataase if your configuration specifies that.

  Here is the full documentation on how to write custom logs in Dorguzen.


      ✅ Dorguzen Logging System — Full Documentation
      -----------------------------------------

The Dorguzen Logging System provides a powerful, configurable, and developer-friendly way to record runtime 
information from your application. It is designed to work both in local development and in restricted shared 
hosting environments where you cannot access PHP error logs (e.g., GoDaddy Shared Hosting).

The logger is implemented through three main classes:

✅ 1. Core Components (Classes Involved)
a) Dorguzen\Configs\Config
-------------

This class provides global framework configuration, including the logging settings:

Logging driver (file, db, or both)

Logging format (text or json)

Log directory path (defaults to storage/logs)


b) Dorguzen\Core\DGZ_Logger
-------------

This is the core logging engine. It:

Accepts log messages at multiple severity levels
(debug, info, warning, error, critical)

Handles file creation, rotation, and concurrency-safe writes

Forwards logs to the database via the Logs model

Supports text and JSON structured logging

Automatically creates missing directories

Uses PSR-3-style method signatures (but simplified)


c) src\models\Logs
-------------

This is the ORM model responsible for database logging.
It now also stores context data in a context_json field (JSON encoded).

This allows you to build structured, filterable logs directly inside your application.



✅ 2. Logging Drivers
--------------------

The behavior of the logger is controlled by:

'log_driver' => 'db',   // file | db | both
'log_format' => 'text', // text | json

These values live inside configs/Config.php.

Driver Options
Driver	Description
file	Writes logs to rotating files under /storage/logs/.
db	Logs messages into your logs database table.
both	Writes to file AND database simultaneously.


✅ 3. Logging Formats
-----------------

You may choose how each line is written when using the file driver.

'log_format' => 'text', // default
// or:
'log_format' => 'json'

The text Output Example
[2025-11-10 15:04:21] ERROR: Payment failed

The json Output Example
{"timestamp":"2025-11-10 15:04:21","level":"ERROR","message":"Payment failed","context":{"txn":5533}}


JSON logs are machine-readable and excellent for future analytics.


✅ 4. How Logging Works (Chronological Explanation)
--------------------

Below is the full flow of how a log message is processed from start to finish.

✅ Step 1 — Logger Initialization

In bootstrap/app.php, your framework initializes the logger:

DGZ_Logger::init(__DIR__ . '/../storage/logs/dgz_errors.log');


During initialization:

The logger builds its internal settings (driver, format, etc.)

Ensures the directory /storage/logs exists

Ensures the directory is writable

Creates a Logs model instance for DB logging

Prepares to create today's log file

✅ Step 2 — Daily Log File Determination

Each day creates its own file:

/storage/logs/dgz-2025-11-10.log


This is called daily log rotation, and it prevents massive single log files from forming.

✅ Step 3 — Writing Logs

When you call:

DGZ_Logger::error("User login failed", ['email' => 'test@example.com']);


The logger:

Normalizes the severity level

Formats the message depending on text/json mode

Writes to file, using flock() for concurrency-safe writes

Writes to DB
via

$logsModel->log($level, $message, $context);


Thanks to the new context_json column, the DB now stores structured context data.


✅ 5. Cool Features of the Logging system
-----------------------------

The Dorguzen logging system is equivalent in spirit to a lightweight version of 
Laravel's Monolog integration, but written natively with zero external dependencies.

Here are its strongest features:

a) Safe Concurrent Writes (flock)

Multiple requests writing at once will not corrupt the log file.

b) Daily Log Rotation

Automatically creates a new file per day:
dgz-YYYY-MM-DD.log

c) Dual Drivers (file, db, both)

  You can:
    -log only to file
    -log only to DB

  or both at the same time

  Right from your configuration.

d) Optional JSON Structured Logging

  This is perfect for servers, analytics tools, and future machine learning integration.

e) Log Levels

  debug, info, warning, error, critical

  Same naming conventions used by PSR-3 and Laravel.

f) PSR-3-Style Method Signatures

  The API feels familiar for anyone used to modern logging libraries:

  DGZ_Logger::error($msg, $context);

g) Integrates Cleanly With Your Logs Model

  All context is stored into context_json, providing:

  -filterable logs
  -human readable output
  -selectable context keys
  -advanced debugging

h) Fully Extensible

  You can easily attach:

  -Email notifications
  -Slack / Discord alerts
  -Webhooks
  -Custom rotating strategies

Because the logger architecture is very clean.


✅ 6. Example Usage
------------------
Basic Log
-----

    DGZ_Logger::info("User logged in");


✅ Log with Context
-----

    DGZ_Logger::warning("Slow DB query", [
        'duration_ms' => 240,
        'sql' => 'SELECT * FROM users'
    ]);


✅ Critical Error
-----

    DGZ_Logger::critical("Payment gateway unavailable", [
        'gateway' => 'Stripe'
    ]);



✅ 7. Example Output
------------------------
File (text mode)
-----
[2025-11-10 15:32:10] INFO: User logged in
[2025-11-10 15:32:12] WARNING: Slow DB query | {"duration_ms":240,"sql":"SELECT * FROM users"}
[2025-11-10 15:32:20] CRITICAL: Payment gateway unavailable | {"gateway":"Stripe"}

File (json mode)
-----
{"timestamp":"2025-11-10 15:32:20","level":"CRITICAL","message":"Payment gateway unavailable","context":{"gateway":"Stripe"}}

Database Row Example
-----
    id    title     message	                      context_json	              logs_created
    1	    CRITICAL	Payment gateway unavailable	  {"gateway":"Stripe"}	      2025-11-10



✅ 8. What This Unlocks (The Future)
-------------------------

Because this system is structured, stable, and extensible, you can now build:

    -Searchable, filterable admin UI
    -Filter logs by:
      level
      date
      keywords
      context keys

    -Trend analytics & monitoring dashboards
    -Plot errors by day, by controller, by model, etc.
    -Machine-learning / anomaly detection

JSON logs make it possible.

    -Rich developer experience

Even on shared hosting with no access to error_log.

    -Automated alert pipelines

Plug-in logic can email you on CRITICAL failures.


Final Thoughts
-----------------

The new Dorguzen Logging System is:

✅ Console-framework-grade
✅ Concurrency safe
✅ Extremely configurable
✅ PSR-3-aligned
✅ Extends neatly without rewriting anything
✅ Equivalent in spirit to a small Monolog engine
✅ Designed for real-world hosting constraints

You may want to clear your logs, sometimes, otherwise it will keep logging for ever.
There you have it; one of the strongest and most flexible subsystems in Dorguzeb-a 
very useful tool for debugging. Happy logging.


   Log Channels
   ============

   So far the examples above all use DGZ_Logger::error() / info() / etc., which write to a single,
   shared destination controlled by APP_LOG_DRIVER and APP_LOG_FORMAT in your .env file.

   A log channel is a named, independently-configured logging destination.
   Instead of one global stream you can have several — one per concern:
       payments  →  file-only, JSON, warnings and above
       security  →  file + DB, JSON, errors and above
       default   →  whatever APP_LOG_DRIVER says (backwards-compatible with all existing DGZ_Logger calls)

   Every channel has its own:
       driver     — where to write: 'file' | 'db' | 'both'
       format     — line format:    'text' | 'json'
       path       — directory for log files, relative to the project root
       min_level  — minimum severity to record: debug | info | notice | warning | error | critical
                    Messages below the threshold are silently discarded.


   Configuring channels
   ---------------------

   Open (or create) configs/logging.php. The ConfigLoader picks it up automatically — no registration needed.

   <?php
   return [
       'channels' => [

           // 'default' mirrors your .env APP_LOG_DRIVER / APP_LOG_FORMAT settings
           // so all existing DGZ_Logger::error() calls are completely unaffected.
           'default' => [
               'driver'          => env('APP_LOG_DRIVER', 'db'),
               'format'          => env('APP_LOG_FORMAT', 'text'),
               'path'            => 'storage/logs',
               'min_level'       => 'debug',
               'filename_prefix' => 'dgz',   // produces dgz-YYYY-MM-DD.log
           ],

           'payments' => [
               'driver'    => 'file',   // file only — never touches the DB
               'format'    => 'json',
               'path'      => 'storage/logs',
               'min_level' => 'warning',
           ],

           'security' => [
               'driver'    => 'both',   // file AND DB
               'format'    => 'json',
               'path'      => 'storage/logs',
               'min_level' => 'error',
           ],

       ],
   ];


   Using channels in your application code
   -----------------------------------------

   Anywhere you have access to DGZ_Logger you can call ::channel():

       DGZ_Logger::channel('payments')->warning('Charge failed', ['amount' => 5000, 'user' => $userId]);
       DGZ_Logger::channel('security')->error('Login brute force', ['ip' => $ip, 'attempts' => 12]);
       DGZ_Logger::channel('security')->critical('Privilege escalation attempt', ['user' => $user]);

   The API is identical to the top-level DGZ_Logger convenience methods (debug/info/notice/warning/error/critical),
   so switching between global and channel-based logging is a one-word change.

   Channel instances are cached per request — calling ::channel('payments') ten times only creates one object.

   File naming
   -----------

   By default a channel writes to:
       {path}/{channelName}-YYYY-MM-DD.log     e.g.  payments-2025-11-10.log

   You can override the filename prefix with the optional filename_prefix key in the channel config:
       'filename_prefix' => 'dgz'    →    dgz-2025-11-10.log

   The 'default' channel uses filename_prefix: 'dgz' so its files match the legacy format
   produced before channels were introduced.


   Filtering DB logs by channel
   ----------------------------

   When driver is 'db' or 'both', the channel name is injected into the context_json column:
       {"_channel": "security", "ip": "1.2.3.4", "attempts": 12}

   This lets you query or filter by channel in phpMyAdmin, your admin panel, or a future log viewer:
       SELECT * FROM logs WHERE context_json LIKE '%"_channel":"security"%';


   Viewing logs via the CLI (php dgz log)
   ----------------------------------------

   The 'php dgz log' command displays recent log entries directly in your terminal:
       php dgz log

   Important: this command reads exclusively from the DB (the logs table). It does not read
   any log files. This means:

     • Channels with driver 'db' or 'both' — their entries appear in the output.
     • Channels with driver 'file'          — their entries do NOT appear; they only exist on disk.

   To make the blind spot visible, the command automatically prints a notice at the bottom of
   its output whenever it detects one or more file-only channels in your config:

       Note: channel(s) [payments] use driver 'file' — their entries are not stored in the DB
             and do not appear above.
             To inspect: php dgz log:tail --channel=payments

   This notice is purely informational — it does not affect the exit code or the log output above it.

   If you want a channel's entries to appear in 'php dgz log', change its driver to 'db' or 'both'
   in configs/logging.php. The trade-off is that 'both' writes every entry twice (file + DB row),
   which adds a small write overhead but gives you both the raw file archive and the structured
   DB view simultaneously.


   Log Tailing
   ===========

   Log tailing lets you watch a log file update in real time in your terminal — identical to running
   `tail -f` manually, but integrated into the Dorguzen CLI so you never have to remember the file path.


   The command
   -------

   Tail the default channel (driver must be 'file' or 'both'):
       php dgz log:tail

   Tail a named channel:
       php dgz log:tail --channel=payments
       php dgz log:tail -c payments

   Show 50 lines of history before following new output (default is 20):
       php dgz log:tail --channel=security --lines=50
       php dgz log:tail -c security -l 50

   The command streams output to your terminal and keeps running until you press Ctrl+C.
   It tells you which file it is watching so there is never any ambiguity.

   Notes:
     • Requires the channel's driver to be 'file' or 'both'. A db-only channel has no file.
     • The log file is created on the first write. If none exists yet, the command tells you what to do.
     • `tail` must be available on the host OS. It is standard on macOS and every Linux distribution.


   How to test it — a complete working example
   ---------------------------------------------

   This is the fastest way to see channels and tailing working end-to-end.
   Open two terminal tabs side-by-side.

   Step 1 — make sure the payments channel writes to a file.
   In configs/logging.php confirm (or set):

       'payments' => [
           'driver'    => 'file',
           'format'    => 'json',
           'path'      => 'storage/logs',
           'min_level' => 'warning',
       ],

   Step 2 — in Terminal A, start tailing:
       php dgz log:tail --channel=payments

   You will see:
       Tailing [payments]: /path/to/project/storage/logs/payments-2025-11-10.log
       Press Ctrl+C to stop.

   If the file does not exist yet the command will print a hint — just continue to Step 3.

   Step 3 — in Terminal B, write a log entry.
   The quickest way is a tiny PHP one-liner from the project root:

       php -r "
           require_once __DIR__ . '/tests/manual/cliTestHeader.php';
          \Dorguzen\Core\DGZ_Logger::channel('payments')->warning('Test charge failed', ['amount' => 100]);
       " 


   Or, add this temporarily to any controller action and visit that page in your browser:

       DGZ_Logger::channel('payments')->warning('Test charge failed', ['amount' => 100]);
       DGZ_Logger::channel('payments')->error('Payment gateway timeout', ['gateway' => 'Stripe']);

   Step 4 — watch Terminal A.
   Each log line appears in Terminal A the moment it is written:
       {"time":"2025-11-10 14:32:01","channel":"payments","level":"WARNING","message":"Test charge failed","context":{"amount":100}}
       {"time":"2025-11-10 14:32:01","channel":"payments","level":"ERROR","message":"Payment gateway timeout","context":{"gateway":"Stripe"}}

   Press Ctrl+C in Terminal A to stop tailing.

   That is it. From here you can add more channels to configs/logging.php for any concern in your
   application — audit trails, slow query logs, third-party API calls — each independently configured
   and independently tailored.


    Lifespan of log files
    ----------------------
   You may be wondering, if we only ever see the file of the current date, what's the use of log files of other dates. 
   What are its use cases?                                       
                                                                                                                                                                                                          
  Here is the thing. The daily log files are primarily for retrospective investigation, not live monitoring. 
  Here are the real-world use cases:                                                              
                                                                                                                                                                                                          
  1. Post-mortem debugging                                                                                                                                                                                
  Something broke at 2am on Tuesday. You wake up Wednesday, see reports, and need to know exactly what happened. 
  You open payments-2026-02-24.log and trace the exact sequence of events — timestamps,    
  context data, everything.                                                                                                                                                                               
                                                                                                                                                                                                          
  2. Incident timeline reconstruction
  A user reports their payment failed 'sometime last week.' Without the old log files you'd have no evidence. 
  With them you can pull up the exact date range and search:

    grep 'user_id.*4521' storage/logs/payments-2026-02-18.log

  3. Spotting patterns across time
  Errors that happen every Monday at 9am (scheduled job clash), or every month-end (heavy load), or only on 
  specific days. You'd never notice this from today's file alone.

  4. Auditing and compliance
  Financial transactions, auth events, and security logs often need to be retained for legal/compliance 
  reasons (30 days, 90 days, 1 year depending on your industry). Camerooncom handles payments, so
  this is relevant.

  5. Comparing before/after a deployment
  You shipped code on Friday. Something seems off now. You compare Saturday's log against Thursday's to see 
  what changed in behaviour.

  ---
  So the question becomes: how long should you keep them?

  The common approach is log rotation with a retention policy — keep N days, then auto-delete. Tools like 
  logrotate (Linux) handle this, or you could add a log:prune --days=30 command to the CLI. Most
  production apps keep 30-90 days of file logs and rely on the DB for longer-term structured querying.

  Since Dorguzen provides you with a DB driver for logs as well, old file logs are mostly a safety net and
  a fast grep-able archive. The DB gives you the structured, queryable view; the files give you the raw,
  never-filtered record.


   Log Pruning
   ===========

   Log pruning lets you enforce a file retention policy from the CLI — deleting log files older than a
   chosen number of days — without touching anything by hand. A DB audit record is always written after
   every run (including dry-runs) so there is a permanent, queryable history of what was pruned and when.


   The command
   -----------

   Prune all channels, delete files older than 30 days (the default):
       php dgz log:prune

   Use a custom cutoff:
       php dgz log:prune --days=7
       php dgz log:prune --days=90

   Limit to one channel:
       php dgz log:prune --channel=payments
       php dgz log:prune -c payments

   Combine options:
       php dgz log:prune --channel=security --days=90

   Preview what would be deleted without touching anything (--dry-run):
       php dgz log:prune --dry-run
       php dgz log:prune --channel=payments --days=7 --dry-run

   List every log file regardless of age (days=0 means "everything is old"):
       php dgz log:prune --days=0 --dry-run


   Options summary
   ---------------

   --days, -d      (default: 30)
       Files whose last-modified time is more than this many days ago are candidates for deletion.
       Use 0 to target every log file.

   --channel, -c   (default: all)
       Restrict pruning to a single named channel. The name must match a key in configs/logging.php.
       Channels with driver 'db' are automatically skipped (they have no files).
       Pass 'all' (or omit the option) to process every file-backed channel.

   --dry-run       (flag, no value)
       Report which files would be deleted without actually deleting them. The DB audit entry is still
       written so you have a record of the intent.


   How channel ownership is determined
   ------------------------------------

   The command identifies which files belong to a channel by filename prefix, not by subdirectory.
   Multiple channels can share the same directory (e.g. storage/logs/) and the command will still
   only touch the files that belong to each channel.

   The prefix follows the same rule used by the logger when creating files:
     • If the channel config has a filename_prefix key, that value is used.
     • Otherwise the channel name itself is the prefix.

   So for the default configs/logging.php setup:
       Channel 'default'  → prefix 'dgz'      → matches dgz-YYYY-MM-DD.log
       Channel 'payments' → prefix 'payments'  → matches payments-YYYY-MM-DD.log
       Channel 'security' → prefix 'security'  → matches security-YYYY-MM-DD.log

   Each channel only sees its own files. A file like payments-2026-02-25.log will never be
   counted or deleted when pruning the 'default' or 'security' channel.

   If you ever configure a channel to use a dedicated subdirectory (e.g. path: storage/logs/payments),
   both the prefix filter and the directory are scoped per channel, so isolation is maintained either way.


   The --dry-run safety net
   ------------------------

   Always run with --dry-run first, especially when setting a new retention window. The output shows
   exactly which files are in scope, their age in days, and the final count:

       [dry-run] No files will be deleted.

       Channel 'default' — scanning /path/to/project/storage/logs
         [would delete] dgz-2025-11-10.log (age: 106d)
         [would delete] dgz-2025-11-11.log (age: 105d)

       Dry run complete. 2 file(s) would be deleted, 3 kept.

   Once you are happy with what you see, run the same command without --dry-run to commit the deletion.


   DB audit record
   ---------------

   After every run (real or dry), the command writes one row to the logs table via Logs::log().
   This bypasses the channel driver setting so the audit ALWAYS reaches the DB, even if you disabled
   DB logging for a channel.

   The row looks like:
       logs_title:   INFO
       logs_message: log:prune — 2 file(s) deleted
       context_json: {"days":30,"channel":"all","deleted":2,"skipped":3,"dry_run":false}

   To query recent prune history:
       SELECT * FROM logs WHERE logs_title = 'INFO'
         AND logs_message LIKE 'log:prune%'
         ORDER BY logs_created DESC;

   Or via the CLI:
       php dgz log


   Recommended retention strategy
   --------------------------------

   File logs — keep for 30 days by default.
       php dgz log:prune --days=30

   High-sensitivity channels (security, payments) — keep longer for compliance.
       php dgz log:prune --channel=security --days=90
       php dgz log:prune --channel=payments --days=90

   DB logs — keep indefinitely. They are compact (no raw text, just structured rows) and are
   the primary source for dashboards and audit queries.

   You can automate pruning with a cron job (Linux/macOS):
       # Run every day at 2am, prune files older than 30 days
       0 2 * * * cd /path/to/project && php dgz log:prune --days=30 >> /dev/null 2>&1


   Graceful edge cases
   -------------------

   • Unknown channel — the command prints an error and exits with a non-zero code.
       php dgz log:prune --channel=unknown
       → [error] Unknown channel 'unknown'. Check configs/logging.php for valid channel names.

   • Channel with driver 'db' — skipped silently with an informational note.
   • Log directory does not exist — skipped with a warning; the command continues with other channels.
   • File permission error — reported per-file; the run continues and the DB audit counts it as skipped.
   • No DB connection — the audit write failure is caught and reported as a warning; the exit code is
     still SUCCESS since the file operations succeeded.




     






      The difference between PHP's error_log() and DGZ_Logger
      --------------------------------------------------------
    You can use either, PHP's error_log() or Dorguzen's DGZ_Logger's log() function to write custom logs,
  but here's a comparison:


    error_log() advantages
    --------------------
  Here are the advantages or using error_logs() for logging:

    Handles file locking internally

    OS-level optimized

    Good for append-only logs

    Less likely to cause race conditions



    file_put_contents() advantages
    --------------------------

    Full control over format

    Can create directories first

    Can rotate logs more easily

For writing logs to a custom file, either is fine — but DGZ's DGZ->Logger::log() works with your config 
settings to manage how you log data-via DB, file, or both. Having the logs in your D can mean that you 
can display them in log report screens for other members of your team to analyse.




    How to write values to console during PHPUnit tests 
    ------------------------------------------------------
Do a try {} and catch block, and in the catch block, throw a RuntimeException exception like this: 

    throw new \RuntimeException(
        'Config WAS loaded successfully in bootstrap/testing.php Config dump: ' .
        json_encode($this->config->getConfig())
    )






    PHPUnit Testing
    ----------------
PHPUnit Testing in Dorguzen

This document explains how PHPUnit works inside Dorguzen, not how PHPUnit works in general.

Dorguzen's testing system is intentionally explicit, predictable, and framework-aware. 
There is no hidden magic. Every test boots exactly what it depends on, and nothing more.

By the end of this section, you will understand:

How Dorguzen boots the test environment

How HTTP and feature tests are dispatched

How request and response state is isolated

How authentication is simulated in tests

What helpers and assertions are available

How to write clean, reliable tests for your application

Testing Philosophy in Dorguzen

Dorguzen follows three core testing principles:

Tests must bootstrap what they depend on

Each test runs in a clean request-response lifecycle

No global state leakage between tests

This means:

No shared request objects

No shared response objects

No “magic” authentication state

No reliance on test order

If a test passes, it passes for the right reason.

Test Entry Point
bootstrap/testing.php

This file is the entry point for all tests.

It is responsible for:

Loading environment variables for testing

Booting framework configuration

Preparing Dorguzen for parallel-safe execution

⚠️ Tests do not use bootstrap/app.php

Instead, all tests boot through bootstrap/testing.php, which ensures:

The correct .env.testing configuration is used

No application state leaks from development or production

This file is loaded inside the base TestCase during setup.

Base Test Case
src/Testing/TestCase.php

All tests extend this class.

Responsibilities:

Bootstraps the test environment

Loads bootstrap/testing.php

Resets framework state between tests

Registers core testing traits

Every test you write ultimately inherits from this class.

Database Resetting
src/Testing/RefreshDatabase.php

This trait provides database isolation between tests by running `migrate:fresh` once per test suite.

What it does:

Drops all application tables

Re-runs all migrations against the test database

Gives every test run a clean, fully-migrated schema

This is wired into the base TestCase automatically — you do not need to add anything to your test
class. The reset happens exactly once per PHP process (the first time any test boots the framework),
and the resulting clean schema is shared across all tests in that run.


class UserTest extends TestCase
{
    use RefreshDatabase;

    // tests...
}

HTTP Testing System

Dorguzen's HTTP testing system mirrors a real request lifecycle — but runs entirely in memory.

Core HTTP Testing Classes
src/Testing/http/
├── DispatchesHttpRequests.php
├── InteractsWithHttp.php
├── KernelResponse.php
├── TestInputStream.php
├── TestResponse.php


Each has a clearly defined responsibility.

Dispatching HTTP Requests
DispatchesHttpRequests (trait)

This trait provides the entry point for HTTP tests.

It exposes helpers like:

$this->get('/ping');
$this->post('/echo', ['message' => 'Hello']);
$this->http('POST', '/login', [...]);


Internally, these helpers:

Prepare the request

Bootstrap the HTTP kernel

Dispatch the request

Capture the response

Return a TestResponse instance

HTTP Glue Layer
InteractsWithHttp (trait)

This trait acts as the glue between PHPUnit and Dorguzen’s HTTP layer.

It:

Prepares request headers

Handles JSON payloads

Injects the test input stream

Returns a fluent TestResponse object

You never call this directly — it powers the higher-level helpers.

Input Stream Handling
TestInputStream

This class wraps php://input for testing.

Why this exists:

php://input is read-once

JSON requests need predictable input

Tests must not interfere with each other

Each HTTP test gets a fresh input stream, ensuring isolation.

Kernel Dispatch for Testing
dispatchForTesting()

The HTTP kernel exposes a dedicated testing entry point.

During each test request:

The request object is reset

The response object is reset

Routes are loaded explicitly

Headers and status codes are cleared

Output buffering is used to capture response body

This guarantees:

One test = one clean request-response lifecycle

Response Handling
KernelResponse

This is a low-level container that holds:

HTTP status code

Headers

Raw response body

It is converted into a TestResponse for assertions.

TestResponse
src/Testing/http/TestResponse.php

This is what your tests interact with.

Available assertions include:

$response->assertStatus(200);
$response->assertJson(['status' => 'ok']);
$response->assertJsonPath('data.email', 'test@example.com');


The response object parses JSON automatically and provides helpful error output when assertions fail.

JSON Testing Helpers

Dorguzen provides first-class JSON testing support.

Example:

$this->postJson('/echo-json', [
    'name' => 'Gustav',
])
->assertStatus(200)
->assertJson([
    'data' => [
        'name' => 'Gustav',
    ],
]);


Internally:

The request content type is set correctly

The JSON body is injected via TestInputStream

The request object exposes the parsed JSON payload

Authentication Helpers (Testing Only)
src/Testing/auth/InteractsWithAuthentication.php

This trait provides authentication simulation, not real authentication.

Key helper:

$this->actingAs($user);


What this does:

Attaches a user object to the request

Makes the user available via request()->user()

Does not log in, set cookies, or touch sessions

This keeps tests fast and deterministic.

Example:

$user = new TestUser([
    'id' => 1,
    'email' => 'test@example.com',
]);

$this->actingAs($user)
     ->get('/me')
     ->assertStatus(200)
     ->assertJson([
         'email' => 'test@example.com',
     ]);

Important Rule: No Persistent Auth State

Authentication state is cleared after every request.

This prevents:

Auth leakage between tests

False positives

Order-dependent failures

If a test needs a user, it must call actingAs() explicitly.

Writing Feature Tests
Example: Ping Test
class PingTest extends TestCase
{
    public function test_ping_endpoint_returns_ok()
    {
        $this->get('/ping')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
            ]);
    }
}

Example: Auth Guard Test
class AuthGuardTest extends TestCase
{
    public function test_guest_cannot_access_me_endpoint()
    {
        $this->get('/me')
            ->assertStatus(401);
    }
}

Example: Authenticated User Test
class MeTest extends TestCase
{
    public function test_authenticated_user_can_access_me_endpoint()
    {
        $user = new TestUser([
            'id' => 1,
            'email' => 'test@example.com',
        ]);

        $this->actingAs($user)
             ->get('/me')
             ->assertStatus(200)
             ->assertJson([
                 'email' => 'test@example.com',
             ]);
    }
}

Test Support Classes
tests/support/TestUser.php

A lightweight user object for testing.

This avoids:

Database dependencies

ORM coupling

Authentication complexity

Unit Tests

Unit tests live under:

tests/unit/


Example:

class SanityTest extends TestCase
{
    public function test_true_is_true()
    {
        $this->assertTrue(true);
    }
}



Running Tests
--------------

To run all tests:

dgz test


This command:

Boots the testing environment

Runs PHPUnit

Supports parallel-safe execution

Uses phpunit.xml

Creating New Tests

Dorguzen provides a test generator command:

TestCommand.php


This scaffolds:

Proper namespaces

Correct base class

Consistent structure

Final Notes

Controllers must never store request or response objects

Always access them via request() and response() helpers

Tests are isolated by design — do not rely on shared state

If a test fails intermittently, it usually means state leakage

Dorguzen's testing system favors clarity over convenience — and that is exactly what makes it reliable.


Environment Isolation in Dorguzen PHPUnit Testing
------------------------------------------
Dorguzen is designed so that application runtime and test runtime are completely isolated from each other. 
This is not an accidental side effect — it is a deliberate architectural choice that protects real application 
data, avoids state leakage, and enables safe, repeatable, and parallel test execution.

“Dorguzen has two completely separate entry points: one for the web application, and one for testing. 
Running tests does not change the web environment, and visiting the web app does not activate testing mode.”

This separation is one of the core guarantees of Dorguzen's testing system.

Two Independent Entry Points

Dorguzen never “switches modes” at runtime. Instead, it uses explicit bootstrapping.

Context	Entry Point	Environment Loaded
Web application	bootstrap/app.php	.env
PHPUnit tests	bootstrap/testing.php	.env.testing

Because these entry points are different, the environments are parallel and independent.

Visiting the application in a browser never loads the testing environment

Running tests never touches the web application environment

Developers do not need to manually toggle anything.



How the Testing Environment Is Activated
----------------------------------------

When a developer runs:

dgz test

PHPUnit bootstraps Dorguzen through:

TestCase::setUp()
└── bootstrap/testing.php

This file:

Loads .env.testing

Loads testing-specific configuration

Prepares the framework for safe, isolated test execution

The normal application bootstrap (bootstrap/app.php) is not involved.

The Role of .env.testing
------------------------

The .env.testing file is mandatory for meaningful testing. It is not a replacement for .env —
it is a set of overrides that are layered on top.

How env loading works in the test environment
---------------------------------------------

EnvLoader always loads files in this order:

  1. .env           ← your development defaults (always loaded first)
  2. .env.testing   ← test overrides (loaded second, wins on any key it defines)

  (.env.local is explicitly skipped when APP_ENV=testing)

This means .env.testing only needs to contain the values that differ from your development
environment. Anything not listed in .env.testing falls through transparently from .env.
You never need to duplicate your full development config into .env.testing.

Default setup — SQLite :memory: (recommended)
----------------------------------------------

The .env.testing file that ships with Dorguzen uses an in-memory SQLite database:

  APP_ENV=testing

  DB_CONNECTION=sqlite
  DB_SQLITE_PATH=:memory:

The :memory: path is a special SQLite keyword. It means the database exists only in RAM for the
duration of the PHP process — nothing is written to disk, and nothing persists between test runs.

Why this is the recommended default:

  ✅ Zero infrastructure — no database server needed
  ✅ Process-local — the web app cannot reach the test database at all
  ✅ Ephemeral — every test run starts from a guaranteed clean slate
  ✅ Fast — RAM is faster than any on-disk database
  ✅ Safe — no risk of accidentally wiping development data

The web app and the test process are in completely separate PHP processes. Because the test
database lives only in the memory of the PHPUnit process, it is structurally impossible for
the web application to access or interfere with it, even if both are running at the same time.


Alternative setup — MySQL/other driver
---------------------------------------

Dorguzen's test infrastructure is fully driver-agnostic. bootstrap/testing.php reads
DB_CONNECTION from the resolved env and instantiates the correct driver automatically:

  DB_CONNECTION=sqlite   → DGZ_SQLiteDriver
  DB_CONNECTION=mysqli   → DGZ_MySQLiDriver
  DB_CONNECTION=pdo      → DGZ_PDODriver
  DB_CONNECTION=postgres → DGZ_PostgresDriver

If you prefer a dedicated MySQL test database, override the relevant values in .env.testing:

  APP_ENV=testing

  DB_CONNECTION=mysqli
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=dorguzen_test    # ← must be a dedicated test-only database
  DB_USERNAME=test_user
  DB_PASSWORD=secret
  DB_KEY=takeThisWith@PinchOfSalt

Notice you only need to list the DB values that differ from .env. All other application config
(app name, URLs, mail settings, modules, etc.) continues to fall through from .env unless you
explicitly override them here.

⚠️  When using a server-based driver, the test database MUST be dedicated and separate from
your development database. RefreshDatabase runs migrate:fresh on every test run — it drops
all tables and rebuilds the schema from scratch. If you point it at your development database
you will lose all your development data.

What else you can add to .env.testing
--------------------------------------

Because .env.testing is just a layered override file, you can add any env key your application
supports. Common additions include:

  # Force debug mode on in tests to surface errors clearly
  APP_DEBUG=true

  # Use synchronous queue processing in tests (no worker needed)
  QUEUE_DRIVER=sync

  # Pin log output to file only, so test output stays clean
  APP_LOG_DRIVER=file

  # Disable modules you don't want active during tests
  MODULES_PAYMENTS_STATUS=off
  MODULES_SMS_STATUS=off

  # Override JWT secret to a known test value
  APP_JWT_SECRET=test-secret-key-not-for-production

  # Exempt API routes from CSRF in tests
  APP_API_CSRF_EXCEPTION='/api/'

  # Cap file uploads to something small for faster tests
  MAX_UPLOAD_FILE_SIZE=1024

Only add what you actually need to change. The principle is: .env.testing should be as short
as possible — just the overrides that make the test environment behave differently from
development.

Database Safety and Testing Best Practices

Dorguzen's testing tools assume that tests are destructive by design.

During testing:

Databases may be wiped

Tables may be dropped

Migrations may be re-run repeatedly

Because of this:

✅ Use a Dedicated Testing Database

Your .env.testing database must not be shared with:

  Local development
  Staging
  Production

This provides a final layer of protection even if a test behaves unexpectedly.


✅ Always Create Migrations for All Tables
Dorguzen tests frequently rely on the built-in command:

  migrate:fresh

This command:

  Drops all tables
  Re-runs all migrations
  Produces a clean database state

For this reason:
  Every table in your application should have a migration.
  Tests cannot safely recreate schema that only exists manually in a database.

Why This Design Matters — True Parallel Isolation
-------------------------------------------------

The web app and the test suite can run simultaneously without interfering with each other.
This is not a soft guarantee enforced by discipline — it is a structural guarantee enforced
by the architecture. Here is why each layer holds:

  Separate processes
  PHPUnit runs in the CLI as its own PHP process. The web app runs under Apache/MAMP as a
  separate PHP process. They share no memory, no container, no session.

  Separate entry points
  The web app boots through bootstrap/app.php. Tests boot through bootstrap/testing.php.
  Neither calls the other. There is no code path that can accidentally cross the boundary.

  Separate env resolution
  APP_ENV=testing is forced via putenv() before EnvLoader is ever called. The web app
  never sees .env.testing. The test process never loads .env.local. Each process arrives
  at a completely independent set of resolved configuration values.

  Separate DB connections
  The resolved DB_CONNECTION determines which driver is instantiated in that process.
  With the default SQLite :memory: setup, the test database lives only in the RAM of the
  PHPUnit process — the web app has no socket, no file, no port to connect to. Even if
  both processes are running at the same time, the test database is structurally
  unreachable from the web app.

  Driver-aware SQL generation
  Blueprint, ColumnDefinition, and the migration infrastructure all generate the correct
  SQL dialect for whichever driver is active (ENGINE=InnoDB for MySQL, no engine clause
  for SQLite, SERIAL for Postgres). Running migrate:fresh in tests produces valid SQLite
  DDL. Running it via php dgz migrate:fresh against the web app produces valid MySQL DDL.
  The same migration files serve both without modification.

By isolating environments at the bootstrap level, Dorguzen avoids an entire class of bugs
and risks:

  ❌ Accidental data loss in development
  ❌ State leaking between tests
  ❌ Config or cache collisions
  ❌ Test mode being exposed to web users
  ❌ Tests interfering with a running web app

Instead, Dorguzen provides:

  ✅ Predictable, order-independent test behavior
  ✅ Safe parallel execution of web app and test suite
  ✅ Clean request/response lifecycles per test
  ✅ Confidence that tests mirror real usage
  ✅ Freedom to run tests without stopping the dev server

A simple way to think about it:

  Running your application and running tests are two different programs.
  They share code, but not state, configuration, environment, or data.

This is the foundation that makes Dorguzen's PHPUnit integration reliable, professional,
and production-safe.

Summary

  -Dorguzen uses explicit environment bootstrapping
  -Web and test environments are fully independent
  -A .env.testing is required and should use a separate database
  -Tests are allowed to freely reset database state
  -Migrations for all your application's tables are essential, for reliable testing

This design ensures that testing in Dorguzen is powerful without ever being dangerous.




Manual testing
----------------
Sometimes when developing software, you often want to do a quick sanity test to convice 
yourself that something is working as it should, or returning some data, and or, in the 
right format/type/structure you expect. That is why in Dorguzen, all that is possible. 
Look at it this way; while PHPUnit is a more official way to prove to others within 
your organisation or an external body that what you wrote works as expected; DGZ manual 
testing is meant to prove to yourself, first of all, and your organisation or colleagues 
that what you have written works. Let's go straight into it.
  The manual testing is a simple cloning of Dorguzen request engine made available for 
you in the command line, via the CliKernel, a child of Dorguzen's Kernel 
(Dorguzen\Core\Kernel\CliKernel). This ensures that you do not have to pass through 
Dorguzen's routing system of controllers and views to test the logic of a class you have 
written. This makes you work faster. This CliKernel bootstraps Dorguzen for you, including 
loading all the config environment, autoloads all your classes, and helper classes, and 
environmental variables etc to make it available to you in the CLI.
  To run manual tests, simply place your test code in the 'tests/manual/ directory'. 
Then navigate in the CLI to the root of your application and run it like so: 

    php tests/manual/testFileName.php
    or
    ./vendor/bin/phpunit tests/unit/LazyLoadTest.php

This test file must always include the 'tests/manual/cliTestHeader.php' file. For 
example:

    require_once __DIR__ . '/cliTestHeader.php';

The code in that included file is what bootstraps Dorguzen, giving you its full power 
there in the CLI.

To see an example in action, see this example manual test file and the others. 

    tests/manual/pdo_test_1.php

They are there to serve as examples of how you can write your own manual tests.
The examples in those manual test files are simple, but feel free to pull in controllers, 
models, services, modules etc to write your test code. 

This whole manual testing system is there so you do not write dummy manual test code in 
your actual application files.

If you have to manually test other things like views, then you whave no choice but to 
temporarilly write test code side-by-side your application code. The idea is to keep 
that to a minimum, and write Unit tests, to make the tests more formal.







——————————————————————
	EXTENSIONS (APIs and Libraries)
——————————————————————

                -Modules
                  -How to create a Module
                    -Adding module configuration to Dorguzen
                    -Adding module middleware to Dorguzen
                    -Routing requests to modules
                      -Modules for auto-discovery routes
                      -Modules for defined routes
                -APIs
                  -Building an API 
                  -The importance of using service classes
                  -Creating an API controller (recommended)
                    -The make:api-controller command
                    -Specifying a version
                    -What the generated controller contains
                    -Adding more methods
                  -Working API Routes
                    -User Registration
                    -User login
                  -How Access tokens work
                -Interactive API Documentation (Swagger UI / OpenAPI)
                  -Overview
                  -How Dorguzen generates the docs — Hybrid Auto-Discovery
                  -Does Dorguzen ship with the required packages?
                  -Should API_DOCS_ENABLED be true or false in production?
                  -Using the Swagger UI to test routes
                  -Understanding access tokens and refresh tokens
                  -Writing OpenAPI annotations (#[OA\*])
                    -1. Global / file-level attributes
                    -2. HTTP method attributes — one per endpoint method
                    -3. Request body — OA\RequestBody and OA\JsonContent
                    -4. Responses — OA\Response
                    -5. Path parameters — OA\Parameter
                    -Full annotated example
                    -Summary of #[OA\*] building blocks
                -Social Media Share Buttons (DGZ_Share)
                  -Supported platforms
                  -Quick start
                  -The shareButtons() helper
                  -Options
                  -Usage examples
                  -Using the class directly
                  -Where the files live
                -Third-Party Service Integrations
                  -Twilio SMS
                    -Setup
                    -How it works
                    -Geographic permissions
                    -Trial accounts
                    -From number
                  -Stripe Payments
                    -Setup
                    -How it works
                    -Confirming payments
                    -Success and cancel URLs
                    -Price IDs

-Here we talk about external tools in the 
  ecosystem of your programming language 
  that make it even more powerful. We are 
  talking here about any existing APIs, 
  development frameworks, and off the shelf  
  libraries available for you to use extend 
  any kind of functionality.





        Modules
        --------

  What is a Module?
  -----------------
  A module in Dorguzen is a self-contained mini-application that lives inside your main application.
Think of it as a fully separated feature area — with its own controllers, models, views, and
services — that plugs cleanly into Dorguzen and works alongside the rest of your app without
tangling up your main codebase.

  Modules are the Dorguzen equivalent of packages or plugins. They are ideal for discrete feature
sets that could, in principle, be extracted and reused across projects: an SEO manager, a payment
gateway, an SMS notification system, a blog engine, a reports dashboard, and so on.

  The key properties of a module:

    - Fully separated  — its files live under modules/{name}/ and are namespaced independently.
    - Mirrors main-app structure — controllers, models, views, services mirror the layout of the
      main application, so there is no new mental model to learn.
    - First-class citizen — module views, models, and controllers have the same capabilities as
      anything in the main application. A module controller extends the same DGZ_Controller base
      class, a module model extends DGZ_Model, and so on.
    - Hooks into app resources easily — a module can read from the shared Config, use any service
      registered in the DI container, call any main-app model, send emails via DGZ_Messenger, use
      helpers, etc. It is separated by convention, not by hard isolation.
    - Toggle on or off — each module can be enabled or disabled with a single flag in .env, with
      no code changes needed anywhere else.


  Folder Structure
  ----------------
  To create a module, add a subdirectory under /modules/ and give it the name of the module
(all lowercase, no spaces). Mirror the main-app folder structure inside it:

        myApplication/
        └── modules/
            └── blog/
                ├── controllers/
                ├── models/
                ├── services/
                └── views/

  You can add as many sub-folders as you need (events/, listeners/, helpers/, etc.). The structure
is a convention, not a hard requirement — Dorguzen only cares about the controllers/ directory
when routing. Everything else is up to you.

  All module controllers must extend the same base controller as the main application:

        Dorguzen\Core\DGZ_Controller

  Module view files are complete HTML templates, identical to those in the main /views directory.
To render a module view, use the dedicated factory method:

        DGZ_View::getModuleView($moduleName, $templateName, $controller, 'html');

  This means module views integrate with the layout system, flash messages, CSRF helpers, and
everything else the main application provides.


  Toggling a Module On or Off
  ---------------------------
  Every module is registered in configs/Config.php under the 'modules' key:

        'modules' => [
            'seo'      => 'on',
            'payments' => 'off',
            'sms'      => 'on',
        ],

  The value is driven by a corresponding .env flag. For example, for an SMS module:

        # .env
        MODULES_SMS_STATUS=on

  And in configs/Config.php:

        'modules' => [
            'sms' => env('MODULES_SMS_STATUS', 'off'),
        ],

  When a module is set to 'off', the router will not resolve any URLs to it and it is completely
inactive. Set it to 'on' and it is available immediately — no code changes, no cache warm-up
beyond clearing the route cache.

  This means you can ship a module in your codebase but keep it dormant until it is needed,
or disable it per-environment (e.g. off in staging, on in production).


  Adding Module Configuration
  ---------------------------
  If your module needs its own configuration values, you have two options.

  Option A — add keys directly into configs/Config.php. Prefix the keys with the module name
to keep things readable:

        // configs/Config.php
        'blog_posts_per_page' => 10,
        'blog_allow_comments' => true,

  Option B (recommended for larger modules) — create a dedicated config file under the
configs/modules/ directory. Dorguzen automatically discovers and merges everything in that
directory into the unified config:

        configs/
        └── modules/
            └── blog.php    ← your module config

  The file should expose a getConfig() method that returns an array:

        // configs/modules/blog.php
        function getConfig(): array {
            return [
                'posts_per_page' => 10,
                'allow_comments' => true,
            ];
        }

  Dorguzen reads and merges this automatically. The values are then globally available anywhere
you call $this->config->getConfig() or the config() helper, just like any other config value.


  Adding Module Middleware
  ------------------------
  If your module needs middleware, place it in the main application's middleware directory — not
inside the module folder. The file name must end with Middleware.php:

        middleware/
        └── globalMiddleware/
            └── BlogMiddleware.php

  DGZ automatically identifies middleware files by this naming convention. Once the file is there,
you can attach it to any route or route group exactly as you would any other middleware.


  Routing Requests to Modules
  ---------------------------
  There are two ways to route requests to a module: auto-discovery and defined routes. Both work
equally well. Choose whichever suits the complexity of your module.


  1. Auto-Discovery Routing
  -------------------------
  With auto-discovery, Dorguzen resolves URLs to module controllers purely by inspecting the URL
segments — no routes need to be registered. The URL format for modules is:

        /{moduleName}/{method}
        /{moduleName}/{subController}/{method}

  Example:

        /blog            → BlogController::defaultAction()
        /blog/latestPosts → BlogController::latestPosts()
        /blog/admin/dashboard → AdminController::dashboard()   (sub-controller — see below)

  For auto-discovery to work, the module must be registered as 'on' in configs/Config.php (see
Toggling above), and must have a default entry controller named after the module:

        modules/blog/controllers/BlogController.php

  This entry controller is the router's gateway into the module. If your module only has one
controller, that is all you need.


  Registering Sub-Controllers (auto-discovery)
  ---------------------------------------------
  When a module has more than one controller, the router needs to know which URL segments refer
to sub-controllers rather than methods. You teach it this by implementing
DGZ_ModuleControllerInterface on the entry controller and declaring the $controllers array:

        use Dorguzen\Core\DGZ_ModuleControllerInterface;
        use Dorguzen\Core\DGZ_ModuleControllerTrait;

        class BlogController extends DGZ_Controller implements DGZ_ModuleControllerInterface
        {
            use DGZ_ModuleControllerTrait;

            protected array $controllers = [
                'AdminController',
                'ApiController',
            ];
        }

  With this in place, the URL /blog/admin/dashboard resolves to AdminController::dashboard()
inside the blog module. Without it, the router would treat 'admin' as a method name on
BlogController and fail.

  URL resolution rules with sub-controllers:

        /blog/admin/dashboard
          └─ module:      blog
          └─ controller:  AdminController   (found in $controllers)
          └─ method:      dashboard()

        /blog/latestPosts
          └─ module:      blog
          └─ controller:  BlogController    ('latestPosts' not in $controllers → it's a method)
          └─ method:      latestPosts()

  Important: a module with only one controller does NOT need DGZ_ModuleControllerInterface at
all. Only add it when you introduce a second controller into the module.


  2. Defined Routes
  -----------------
  Defined routes give you full, explicit control. They work for any controller in any module,
including sub-controllers, with no need for DGZ_ModuleControllerInterface.

  Pass the controller name as the action and the module name as the third argument:

        // routes/web.php
        $router->get('/blog/latest',         'BlogController@latestPosts',  'blog');
        $router->get('/blog/admin/dashboard', 'AdminController@dashboard',  'blog');
        $router->post('/blog/admin/savePost', 'AdminController@savePost',   'blog');

  DGZ resolves the controller class as:

        Dorguzen\Modules\Blog\Controllers\BlogController
        Dorguzen\Modules\Blog\Controllers\AdminController

  The module name (third argument) provides the namespace root; the controller name in the
action string identifies the class within it.

  With defined routes you do not register the module in configs/Config.php — the route
definition itself is the authoritative source of where to find the controller. The 'on'/'off'
toggle still works as a coarse on/off switch for auto-discovery, but defined routes bypass it.


  Which approach should I use?
  ----------------------------
  Use auto-discovery when:
    - You are prototyping or building quickly.
    - Your module URLs follow a simple, predictable pattern.
    - You want zero route-file maintenance as the module grows.

  Use defined routes when:
    - You need precise control over URL shapes.
    - The module exposes a REST-style API.
    - You need middleware on specific module routes.
    - You want the routing to be self-documenting in routes/web.php.

  Both approaches can coexist: some module routes defined explicitly, others auto-discovered.


        
   


        
        
        APIs
        -----
  Dorguzen makes building an API easy. 



        Building an API 
        -------------------
  This is the easier and new approach for creating APIs. The API routes should be defined in:

    routes/api.php

You have to use the dedicated API methods on the router like:
    $router->apiPost() for POST requests
    $router->apiGet() for GET requests
    $router->apiPut() for PUT requests
    $router->apiDelete() for DELETE requests 
    
These methods accept three mandatory arguments; 
    -1) the request URI string e.g. '/api/ads/favs/{id}'
    -2) the controller name and the method (separated by @) e.g. 'AdController@favourites'
    -3) the version number.

Do it like so:

    $router->apiPost('/api/ads/favs/{id}', 'AdController@favourites', 'v1'); 

You must provide all three arguments. The version number (v*) is especially important —
Dorguzen will throw an exception if it is missing. Versioning is enforced as good practice
so that your API can evolve without breaking existing clients. The API 
directory structure in Dorguzen looks like this:

      myApplication/
        |__src/
            └── api/
                ├── v1/
                  └── controllers
                        └── apiController.php
                        └── blogController.php
                        └── adController.php
                ├── v2/
                  └── controllers
                        └── apiController.php
                        └── blogController.php
                        └── adController.php
            ├── controllers/
            ├── models/
            ├── services/

Note that this directory system given above is in the main Dorguzen application directory and
not in the modules sub-directory.
Any of the controllers in your API application can handle any request, so here, there is no
notion of a main/landing controller, as it is in modules.

To create a controller in this directory structure, use the dedicated CLI command (recommended):

    php dgz make:api-controller ControllerName

This is the recommended approach — it generates the controller in the correct directory,
pre-wired with DGZ_APITrait, OpenAPI imports, and a documented annotation skeleton.
See 'Creating an API controller (recommended)' below for full details.

Also, any controller class in the API application that needs to enforce JWT authentication must 
use the DGZ_APITrait trait that provides methods from the Firebase PHP JWT library. 







The importance of using service classes
---------------------------------------
To maintain a clean, scalable architecture, it's highly recommended that your API controllers delegate 
logic to service classes.

Example structure:

  myApplication/
        |__src/
            └── api/
                ├── v1/
                  └── controllers
                        └── apiController.php
                        └── blogController.php
                        └── adController.php
            ├── controllers/
            ├── models/
            ├── services/
                └── AuthService.php
                └── Product.php

  Your API application and your main application's controllers can both turn to service classes for the
  business logic and data processing of your application. This means that, if your main app's controllers 
  have to handle all logic and requests from your web application, which you may have to repeat 
  in your API controllers, or if your API controllers have to talk to your main controllers, which will have 
  to determine if the request is from the web app or the API in order to know how to respond, it will be 
  too messy. The best solution is to use the concept of centralized service classes that both your web app 
  and API talks to. 
    In such a setup; your main web application's as well as your API's controllers will handle HTTP requests 
  and responses. Services will handle the actual business logic and data access. These controllers will 
  therefore be lean and easy to read. Services will receive arguments from both sources and spit back data, 
  and it won't matter who the request is from.
    This approach keeps your API code loosely coupled from your main web app (/src/controllers/), making it
  easier to maintain, scale, or even extract into microservices later. Your API controllers
  in src/api/ can pull from the exact same service classes as your web controllers.




        Creating an API controller (recommended)
        -----------------------------------------
  The recommended way to create an API controller in Dorguzen is with the built-in CLI command:

      php dgz make:api-controller ControllerName

  This is preferred over creating the file by hand because the generated controller comes
  pre-wired with everything you need out of the box:

    - The correct namespace for the target API version
    - DGZ_APITrait already imported and applied (provides validateToken(), setHeaders(), etc.)
    - use OpenApi\Attributes as OA already imported
    - A fully commented OpenAPI annotation skeleton so you can document your first endpoint
      without having to look anything up
    - Inline guidance (in comments) on how to write annotations, handle auth, inject services,
      and register the matching route in routes/api.php



  The make:api-controller command
  --------------------------------
  Basic usage — creates the controller in src/api/v1/controllers/:

      php dgz make:api-controller Product

  This produces: src/api/v1/controllers/ProductController.php



  Specifying a version
  ---------------------
  Use the --api-version option to target a different API version. If the version directory does
  not yet exist, Dorguzen creates it for you automatically:

      php dgz make:api-controller Product --api-version=v2

  This produces: src/api/v2/controllers/ProductController.php
                 (and creates src/api/v2/controllers/ if it did not exist)

  You can pass the version with or without the 'v' prefix — both are accepted:

      php dgz make:api-controller Product --api-version=2    // same result as --api-version=v2



  What the generated controller contains
  ----------------------------------------
  After running the command you will find the following already in place:

    1. Correct namespace
         e.g. namespace Dorguzen\Api\V1\Controllers;

    2. DGZ_APITrait applied
         Gives you validateToken(), setHeaders(), generateTokens(), saveRefreshToken() etc.
         See the DGZ_APITrait section for full method reference.

    3. OpenAPI import
         use OpenApi\Attributes as OA;

    4. A docblock on the class showing example route registrations for all four HTTP verbs,
       ready to copy into routes/api.php.

    5. An index() example method with:
         - A fully commented #[OA\Get(...)] annotation skeleton with every field explained
         - The validateToken() / $this->validatedToken pattern shown in comments
         - A DGZ_Response success response wired up and ready to fill in

    6. After creation the CLI prints the next steps:
         - Register your routes in routes/api.php
         - Replace the TODO placeholders in the annotations
         - Inject any service classes you need via the constructor



  Adding more methods
  --------------------
  The index() method in the stub is just a starting point — it is not special in any way.
  You are free to:

    - Rename it to anything that suits your resource (show, store, update, destroy, etc.)
    - Delete it entirely and write your own methods from scratch
    - Add as many additional methods as your resource needs

  Each method simply needs a corresponding route in routes/api.php and, optionally, an
  #[OA\*] annotation above it for full Swagger UI documentation. If you skip the annotation
  the route will still appear in the docs as an auto-discovered stub automatically.

  Example of a fully built-out controller with multiple methods:

      // routes/api.php
      $router->apiGet('/api/v1/products',      'ProductApi@index',   'v1');
      $router->apiGet('/api/v1/products/{id}', 'ProductApi@show',    'v1');
      $router->apiPost('/api/v1/products',     'ProductApi@store',   'v1');
      $router->apiDelete('/api/v1/products/{id}','ProductApi@destroy','v1');

      // src/api/v1/controllers/ProductApiController.php
      public function index(): void   { /* list all products   */ }
      public function show(): void    { /* get one product      */ }
      public function store(): void   { /* create a product     */ }
      public function destroy(): void { /* delete a product     */ }






  WORKING API ROUTES
  --------------------
  USER REGISTRATION
  ------------
  Here is the request to send (request made with HttPie, an API testing tool in the CLI) to register a user:

  http POST http://localhost/yourAppName/api/v1/auth/register \                               
    firstname=Test \
    surname=User \
    username=testUser \
    password=Secret123! \
    confirm_password=Secret123! \
    phone=12345678 \
    email=testuser@example.com

  Here is the response you will get back:

    HTTP/1.1 201 Created
    Access-Control-Allow-Credentials: true
    Access-Control-Allow-Methods: GET, POST, PATCH, DELETE
    Access-Control-Allow-Origin: https://camerooncom.com/
    Access-Control-Max-Age: 3600
    Cache-Control: no-store, no-cache, must-revalidate
    Connection: Keep-Alive
    Content-Length: 902
    Content-Type: application/json; charset=UTF-8
    Date: Wed, 25 Mar 2026 12:38:43 GMT
    Expires: Thu, 19 Nov 1981 08:52:00 GMT
    Keep-Alive: timeout=5, max=100
    Pragma: no-cache
    Server: Apache/2.4.54 (Unix) OpenSSL/1.0.2u PHP/8.2.0 mod_wsgi/3.5 Python/2.7.18 mod_fastcgi/mod_fastcgi-SNAP-0910052141 mod_perl/2.0.11 Perl/v5.30.1
    Set-Cookie: PHPSESSID=hvclcnqpltj0pb4nvkg30jibd2; path=/
    X-Powered-By: PHP/8.2.0

    {
        "activationLink": "https://camerooncom.com/auth/verifyEmail?em=3c02317a3505459d16826a5e0ef128d3",
        "code": 201,
        "message": "Registration successful. Please check your email to activate your account.",
        "status": true,
        "tokens": {
            "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iLCJhdWQiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iLCJpYXQiOjE3NzQ0NDIzMjgsImV4cCI6MTc3NDQ2MDMyOCwiZGF0YSI6eyJ1c2VyX2lkIjoxMDZ9fQ.pctSfeHz1j6VfkraGx__CNo7jmEezvCck8CXa8lvg_Y",
            "access_token_expiry": 1774460328,
            "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iLCJhdWQiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iLCJpYXQiOjE3NzQ0NDIzMjgsImV4cCI6MTc3NDQ0OTUyOCwiZGF0YSI6eyJ1c2VyX2lkIjoxMDZ9fQ.ba9ltCViZMTuC22dtTLx4FwfLbzOOpuGG9R16rex8_E",
            "refresh_token_expiry": 1774449528
        }
    }

These tokens "tokens": {...} are meant to be stored safely as "access_token" will be needed to be sent with 
subsequent requests to protected routes on the API. 
If the "access_token" expires, the application will require the user to send the "refresh_token" which will 
be used to validate them so they can be issued a new access_token. 


The user will click on the link sent via the email to activate their account. If you are testing and wish 
to simulate the activation of the account, take that 'activationLink' sent back and visit this link locally:

    http://localhost/camerooncom/auth/verifyEmail?em=3c02317a3505459d16826a5e0ef128d3

  and you should get back a success message like this:

    "Your email was successfully activated, you may now log in"




  USER LOGIN 
  ----------
  Login the test user registered in the restration API route above.
This is the request to send (request made with HttPie, an API testing tool in the CLI) to login the testUser
created by the register request above:

    http POST http://localhost/camerooncom/api/v1/auth/login username=testUser password=Secret123!

Here is the response you will get back:

    HTTP/1.1 200 OK
    Access-Control-Allow-Credentials: true
    Access-Control-Allow-Methods: GET, POST, PATCH, DELETE
    Access-Control-Allow-Origin: https://camerooncom.com/
    Access-Control-Max-Age: 3600
    Cache-Control: no-store, no-cache, must-revalidate
    Connection: Keep-Alive
    Content-Length: 904
    Content-Type: application/json; charset=UTF-8
    Date: Wed, 25 Mar 2026 13:56:58 GMT
    Expires: Thu, 19 Nov 1981 08:52:00 GMT
    Keep-Alive: timeout=5, max=100
    Pragma: no-cache
    Server: Apache/2.4.54 (Unix) OpenSSL/1.0.2u PHP/8.2.0 mod_wsgi/3.5 Python/2.7.18 mod_fastcgi/mod_fastcgi-SNAP-0910052141 mod_perl/2.0.11 Perl/v5.30.1
    Set-Cookie: PHPSESSID=2c8hok4iu3eisqsmrggb7daqcp; path=/
    X-Powered-By: PHP/8.2.0

    {
        "code": 200,
        "message": "Login successful.",
        "status": true,
        "tokens": {
            "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iLCJhdWQiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iLCJpYXQiOjE3NzQ0NDcwMjUsImV4cCI6MTc3NDQ2NTAyNSwiZGF0YSI6eyJ1c2VyX2lkIjoxMDZ9fQ.Gc2DaU9UV6LTMta-qvVSsDr7KDr2ewdKWfyvzu49HJ0",
            "access_token_expiry": 1774465025,
            "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iLCJhdWQiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iLCJpYXQiOjE3NzQ0NDcwMjUsImV4cCI6MTc3NDQ1NDIyNSwiZGF0YSI6eyJ1c2VyX2lkIjoxMDZ9fQ.kzKfXk6X1BIjZq9zpZUdNvBUl9vd1RZ6apBU2xqO5I4",
            "refresh_token_expiry": 1774454225
        },
        "user": {
            "email": "testuser@example.com",
            "firstname": "Test",
            "id": 106,
            "lastname": "User",
            "username": "testUser"
        }
    }




  HOW ACCESS TOKENS WORK
  -----------------------
  These requests (register and login) are not secured requests-as in, we do not need to submit any 
tokens with them. But when we registered the user in the register request, an access_token and refresh_token 
was returned. Was that necessary? Because after logging in now i got another set of access token and refresh 
token: 

    {                                                                                               
        "code": 200,                                                                                                                                         
        "message": "Login successful.",                                                                                                                      
        "status": true,                                                                                                                                      
        "tokens": {                                                                                                                                          
            "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iLCJhdWQiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iLC 
    JpYXQiOjE3NzQ0NDcwMjUsImV4cCI6MTc3NDQ2NTAyNSwiZGF0YSI6eyJ1c2VyX2lkIjoxMDZ9fQ.Gc2DaU9UV6LTMta-qvVSsDr7KDr2ewdKWfyvzu49HJ0",                               
            "access_token_expiry": 1774465025,                                                                                                               
            "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iLCJhdWQiOiJodHRwczovL2NhbWVyb29uY29tLmNvbS8iL 
    CJpYXQiOjE3NzQ0NDcwMjUsImV4cCI6MTc3NDQ1NDIyNSwiZGF0YSI6eyJ1c2VyX2lkIjoxMDZ9fQ.kzKfXk6X1BIjZq9zpZUdNvBUl9vd1RZ6apBU2xqO5I4",                              
            "refresh_token_expiry": 1774454225                                                                                                               
        },                                                                                                                                                   
        "user": {                                                                                                                                            
            "email": "testuser@example.com",                                                                                                                 
            "firstname": "Test",                                                                                                                             
            "id": 106,                                                                                                                                       
            "lastname": "User",                                                                                                                              
            "username": "testUser"                                                                                                                           
        }                                                                                                                                                    
    } 
    
  So when registering the user, was it necessary to send back tokens from the server? I can understand it after 
logging in, because that's what will be used by the server to authenticate the user as they make more requests                                                                                   

⏺ It's a design choice, and both approaches are valid — but you're right to question it. Here's the reasoning 
  for each:

  Returning tokens on register (current behaviour):                                                                                                      
    The idea is to save the user an extra round-trip. They just registered — they're clearly authenticated — so 
    you issue tokens immediately and they can start making authenticated API calls without a separate login step. 
    Apps like Spotify and Twitter do this. The UX benefit is the client doesn't need to
    call /login right after /register.                       
                                                                                                                                                         
  Not returning tokens on register:                         
    Simpler and arguably more correct when email verification is required — which you have. The user registered 
    but their email isn't verified yet (emailverified = 'no'). Issuing them a fully valid access token before 
    they've verified their email means they can make authenticated API calls on an  
    unverified account. That may or may not be a problem depending on what your protected routes allow.
                                                                                                                                                         
  My recommendation for apps doing email verification:                        
    If you send an activation email on register, don't issue tokens on register. Return just the success 
    message and activation link. The user verifies their email, then calls /login to get tokens. This is 
    cleaner and avoids the question of "should unverified users be able to hit protected endpoints?" 
   
    to do that will be a small change — just remove the token generation from register() and keep the 201 
    response as confirmation only.

  The token after login overrides the previously issued token. 
  If you look at the login flow in AuthApiController::login():                                                                                    
                                         
      $existing = $this->getRefreshToken($userId);                                                                                                           
      if ($existing) {                                                                                                                                       
          $this->updateRefreshToken($userId, $tokens['refresh_token'], $tokens['refresh_token_expiry']);                                                     
      } else {                                                                                                                                               
          $this->saveRefreshToken($userId, $tokens['refresh_token'], $tokens['refresh_token_expiry']);
      }                                                                                                                                                      
                                                            
  On login it checks if a refresh token row already exists for that user and updates it rather than 
  nserting a new one. So the dgz_refresh_tokens table always has at most one row per user — the most 
  recently issued token. The old one from registration is gone.
                                                                                                                                                         
  The access token isn't stored server-side at all (that's by design with JWT — the server is stateless
  for access tokens), so there's nothing to override there. The client just discards the old one and uses
  the new one.




  INTERACTIVE API DOCUMENTATION (Swagger UI / OpenAPI)
  ======================================================

  Overview
  --------
  Dorguzen ships with built-in, interactive API documentation powered by the OpenAPI standard and
  Swagger UI. When enabled, visiting the docs URL in any browser gives you a live, clickable page
  that lists every API endpoint, shows what each one expects as input, what it returns, and lets
  you fire real test requests directly from the browser — no separate tool needed.

  This is the same experience you get with Django REST Framework's /docs, or Laravel's l5-swagger
  package — but Dorguzen goes one step further with what is called hybrid auto-discovery, explained
  below.

  The docs URL is:

      /api/v1/docs        → the interactive Swagger UI page
      /api/v1/docs/spec   → the raw OpenAPI JSON spec (used by the UI internally)


  How Dorguzen generates the docs — Hybrid Auto-Discovery
  --------------------------------------------------------
  Dorguzen builds the API documentation in two passes:

  PASS 1 — Annotation scan
    Dorguzen uses the zircote/swagger-php library to scan every file in src/api/v1/controllers/
    and collect any PHP 8 OpenAPI attributes (#[OA\*]) that the developer has written on their
    controller methods. These produce rich, fully documented entries in the Swagger UI — with
    descriptions, request body schemas, response examples, and security requirements all shown
    clearly.

  PASS 2 — Router inspection (auto-discovery)
    After the annotation scan, Dorguzen walks through every route registered in routes/api.php
    and checks whether each route already has an annotated entry from Pass 1. Any route that
    does NOT have an annotation yet is automatically added to the spec as a stub entry, tagged
    "Auto-discovered".

  The result: every API route is always visible in the Swagger UI from the moment you add it to
  routes/api.php — even if you have not written a single annotation yet. You never have to worry
  about a route being invisible in the docs. As you add #[OA\*] annotations over time, the stubs
  get replaced with rich documentation.

  This is what makes Dorguzen's approach better than e.g. Laravel's l5-swagger: with Laravel, if
  you haven't annotated an endpoint, it simply does not appear. With Dorguzen, all routes appear
  automatically; annotations are the polish, not the prerequisite.


  Does Dorguzen ship with the required packages?
  -----------------------------------------------
  Yes. The zircote/swagger-php package is included in Dorguzen's composer.json and will be
  installed automatically when you run:

      composer install

  There is nothing extra to install. Just ensure you have set API_DOCS_ENABLED=true in your .env
  file and the docs page will be live.

  One important note: zircote/swagger-php depends on symfony/finder. Because different versions
  of symfony/finder have different PHP version requirements, Dorguzen pins it to a version
  compatible with PHP >= 8.2. If you ever run into a platform conflict after a composer update,
  re-pin with:

      composer require "symfony/finder:^7.3" --update-with-dependencies


  Should API_DOCS_ENABLED be true or false in production?
  --------------------------------------------------------
  This is a design decision that depends on what kind of API you are building:

  For a PUBLIC API (one meant to be consumed by third-party developers):
    Set API_DOCS_ENABLED=true in production. Exposing your docs publicly is not only fine, it
    is the right thing to do. Stripe, Twilio, GitHub, and virtually every developer-facing API
    in the world publishes its docs publicly. The spec reveals no secrets — it only describes
    what your API accepts and returns, which is exactly what a consumer needs to know.

  For a PRIVATE or INTERNAL API (only consumed by your own front-end or mobile app):
    Set API_DOCS_ENABLED=false in production. There is no reason to expose the docs to the
    public, and hiding the spec reduces the surface area that a bad actor could use to map out
    your endpoints.

  The default in .env.example is API_DOCS_ENABLED=true, which is appropriate for local
  development and staging. Set it as suits your deployment in production.


  Using the Swagger UI to test routes
  -------------------------------------
  When you visit /api/v1/docs you will see the Swagger UI page. Here is how to use it:

  1. Expand any endpoint by clicking on it. You will see its method (GET/POST etc.), path,
     description, and the expected request body or parameters.

  2. Click "Try it out" to enable the input fields for that endpoint.

  3. For endpoints that do NOT require authentication (e.g. /api/v1/auth/login and
     /api/v1/auth/register), fill in the JSON body and click Execute. The real HTTP request
     is made and the response shown immediately below.

  4. For endpoints that require a Bearer token (a closed padlock icon appears on them):
     a. First call POST /api/v1/auth/login to obtain a fresh access_token.
     b. Copy the access_token value from the response (just the token string, not "Bearer ...").
     c. Click the green "Authorize" button at the top right of the page.
     d. In the bearerAuth field, paste the token and click Authorize, then Close.
     e. All subsequent "Try it out" requests will now include the Authorization: Bearer header
        automatically. The padlock icons on protected endpoints will appear closed/locked.

  The Swagger UI is configured with persistAuthorization: true, which means your token is
  remembered across page reloads (stored in the browser's local storage for that tab). You do
  not have to re-enter it every time.


  Understanding access tokens and refresh tokens
  -----------------------------------------------
  When a user logs in (POST /api/v1/auth/login), Dorguzen issues two tokens:

  access_token
    A short-lived JWT (JSON Web Token). This is sent with every protected API request in the
    Authorization header like so:

        Authorization: Bearer eyJ0eXAiOiJKV1Qi...

    The server does not store this token anywhere. It simply decodes it on each request using
    the JWT secret key. If the token is valid and not expired, the request proceeds. This is
    what makes JWT-based APIs stateless and fast — no database lookup needed for each request.

    The access_token expires quickly (typically in a few hours). This short lifespan limits the
    damage if a token is stolen — it becomes useless after expiry.

  refresh_token
    A longer-lived token. It is stored server-side in the dgz_refresh_tokens table (one row per
    user — never more). Its purpose is to let the client obtain a new access_token when the old
    one expires, without asking the user to log in again.

    The intended flow is:
      - Client makes a request with the access_token.
      - Server responds with 401 "Access token is expired".
      - Client silently calls a token-refresh endpoint, submitting the refresh_token.
      - Server validates the refresh_token, issues a fresh access_token (and optionally a new
        refresh_token), and the client continues seamlessly.
      - The user never sees a login prompt unless the refresh_token itself has also expired.

    The refresh_token is NOT sent with every request — only when the access_token has expired.

  What happens when a user logs in again?
    Each call to POST /api/v1/auth/login generates a brand-new access_token and refresh_token
    pair. Dorguzen checks whether a refresh_token row already exists for that user in the
    database. If one exists, it is UPDATED with the new token; if not, a new row is inserted.
    The result is that the dgz_refresh_tokens table always holds at most one row per user —
    the most recently issued refresh_token. Any previous refresh_token is therefore invalidated
    at login.

    The access_token has no server-side record to override — the client simply discards the
    old one and uses the new one.

  Tokens issued on registration
    When a new user registers (POST /api/v1/auth/register), Dorguzen also issues an access_token
    and refresh_token immediately. The rationale is convenience: the user just proved who they
    are by submitting their credentials, so there is no need to force a separate login call.
    Many APIs (Spotify, Twitter/X, etc.) follow this same pattern.

    The trade-off is that the token is issued before the user has verified their email. Whether
    that matters depends on what your protected endpoints do. If sensitive actions require email
    verification, enforce that check inside the endpoint logic.


  Writing OpenAPI annotations (#[OA\*])
  ======================================
  Dorguzen uses PHP 8 native attributes (the #[...] syntax) to write OpenAPI documentation
  directly in the controller source code, co-located with the method they document. The library
  that reads these attributes and produces the OpenAPI JSON spec is zircote/swagger-php.

  You do NOT have to write any annotations to get your routes visible in the docs — auto-
  discovery handles that. Annotations are for when you want to go beyond the auto-generated
  stub and give consumers a clear contract: what fields the request body expects, what each
  response code means, and what the response body looks like.

  The namespace to import at the top of your controller file is:

      use OpenApi\Attributes as OA;

  Below is a breakdown of every annotation building block you will use, with examples.


  1. Global / file-level attributes
  -----------------------------------
  These go on a class (not a method) and are written exactly once in your entire codebase.
  Dorguzen places them on DocsController.

  #[OA\Info(...)]       — the top-level metadata block for the whole spec

      #[OA\Info(
          version: '1.0.0',
          title: 'My App API',
          description: 'A brief description of what this API does.'
      )]

  #[OA\SecurityScheme(...)]   — defines an authentication scheme (e.g. Bearer JWT)

      #[OA\SecurityScheme(
          securityScheme: 'bearerAuth',   // the name you reference in endpoint annotations
          type: 'http',
          scheme: 'bearer',
          bearerFormat: 'JWT',
          description: 'Paste your access_token here. Obtain one via POST /api/v1/auth/login.'
      )]

  You do not need to touch these unless you are changing the app name or adding a second auth
  scheme. They live in src/api/v1/controllers/DocsController.php.


  2. HTTP method attributes — one per endpoint method
  ------------------------------------------------------
  Each endpoint method in your API controller gets one of these:

      #[OA\Get(...)]      for GET requests
      #[OA\Post(...)]     for POST requests
      #[OA\Put(...)]      for PUT requests
      #[OA\Patch(...)]    for PATCH requests
      #[OA\Delete(...)]   for DELETE requests

  Common parameters shared by all of them:

      path         The URI exactly as registered in routes/api.php
                   e.g. '/api/v1/user/favourites'

      operationId  A unique camelCase identifier across the whole spec — no spaces or slashes.
                   e.g. 'getUserFavourites'

      summary      A short one-line description shown as the endpoint title in the UI.

      description  A longer explanation (optional). Supports markdown.

      tags         An array of strings that groups endpoints into collapsible sections in the UI.
                   e.g. ['Favourites'] or ['Authentication']

      security     Required for protected endpoints. References the security scheme name you
                   defined in #[OA\SecurityScheme]. Leave this out for public endpoints.
                   e.g. security: [['bearerAuth' => []]]

  Full example — a protected GET endpoint:

      #[OA\Get(
          path: '/api/v1/user/favourites',
          operationId: 'getUserFavourites',
          summary: "Get the authenticated user's favourited ads",
          description: 'Returns all ads the authenticated user has saved to their favourites list.',
          tags: ['Favourites'],
          security: [['bearerAuth' => []]],
          responses: [
              new OA\Response(response: 200, description: 'Favourites retrieved successfully'),
              new OA\Response(response: 401, description: 'Unauthorised — missing or expired token'),
          ]
      )]
      public function index(): void { ... }


  3. Request body — OA\RequestBody and OA\JsonContent
  -----------------------------------------------------
  Used on POST/PUT/PATCH endpoints that accept a JSON body.

      requestBody: new OA\RequestBody(
          required: true,
          content: new OA\JsonContent(
              required: ['username', 'password'],   // list the required field names
              properties: [
                  new OA\Property(property: 'username', type: 'string',  example: 'johndoe'),
                  new OA\Property(property: 'password', type: 'string',  format: 'password', example: 'Secret123!'),
              ]
          )
      )

  OA\Property parameters:
      property    The JSON field name
      type        'string', 'integer', 'boolean', 'array', 'object', 'number'
      format      Optional hint: 'email', 'password', 'date', 'uri', 'int64', etc.
      example     A sample value shown in the UI and in "Try it out" pre-filled fields


  4. Responses — OA\Response
  ----------------------------
  Every endpoint annotation must include at least one response. Each uses:

      new OA\Response(
          response: 200,                  // the HTTP status code as an integer
          description: 'Success',         // plain-text description of this response
          content: new OA\JsonContent(    // optional — describe the response body shape
              properties: [
                  new OA\Property(property: 'code',   type: 'integer', example: 200),
                  new OA\Property(property: 'status', type: 'boolean', example: true),
                  new OA\Property(property: 'data',   type: 'array',   items: new OA\Items(type: 'object')),
              ]
          )
      )

  The content block is optional. For error responses a plain description is usually enough:

      new OA\Response(response: 401, description: 'Unauthorised — invalid or expired token'),
      new OA\Response(response: 422, description: 'Validation failed — see message for details'),
      new OA\Response(response: 500, description: 'Server error'),


  5. Path parameters — OA\Parameter
  ------------------------------------
  When your route contains a {placeholder} like '/api/v1/ads/{id}', document it like this:

      parameters: [
          new OA\Parameter(
              name: 'id',
              in: 'path',
              required: true,
              description: 'The ID of the ad',
              schema: new OA\Schema(type: 'integer', example: 42)
          )
      ]

  The in: field can be 'path', 'query', 'header', or 'cookie'.


  Full annotated example — a POST endpoint with request body and responses
  -------------------------------------------------------------------------

      use OpenApi\Attributes as OA;

      #[OA\Post(
          path: '/api/v1/auth/login',
          operationId: 'authLogin',
          summary: 'Log in an existing user',
          description: 'Authenticates the user by username and password, returning JWT tokens.',
          tags: ['Authentication'],
          requestBody: new OA\RequestBody(
              required: true,
              content: new OA\JsonContent(
                  required: ['username', 'password'],
                  properties: [
                      new OA\Property(property: 'username', type: 'string', example: 'johndoe'),
                      new OA\Property(property: 'password', type: 'string', format: 'password', example: 'Secret123!'),
                  ]
              )
          ),
          responses: [
              new OA\Response(
                  response: 200,
                  description: 'Login successful',
                  content: new OA\JsonContent(properties: [
                      new OA\Property(property: 'code',         type: 'integer', example: 200),
                      new OA\Property(property: 'status',       type: 'boolean', example: true),
                      new OA\Property(property: 'message',      type: 'string',  example: 'Login successful'),
                      new OA\Property(property: 'access_token', type: 'string'),
                  ])
              ),
              new OA\Response(response: 401, description: 'Invalid credentials'),
              new OA\Response(response: 422, description: 'Missing username or password'),
          ]
      )]
      public function login(): void
      {
          // controller logic here
      }


  Summary of #[OA\*] building blocks
  ------------------------------------

      Attribute               Purpose
      ─────────────────────── ───────────────────────────────────────────────────────
      OA\Info                 Top-level spec metadata (title, version, description)
      OA\SecurityScheme       Defines an auth scheme (Bearer JWT, API key, etc.)
      OA\Get / Post / etc.    Documents one endpoint method
      OA\RequestBody          Describes the request body for POST/PUT/PATCH
      OA\JsonContent          The JSON shape of a request or response body
      OA\Property             One field within a JsonContent schema
      OA\Items                The schema of array items inside an OA\Property of type array
      OA\Response             One possible HTTP response for an endpoint
      OA\Parameter            A path, query, header, or cookie parameter









——————————————————————
	NETWORKING
——————————————————————

          -Intro
          -Understanding DSN


Intro
——-
 In today's connected world, most applications don't live in isolation — they constantly communicate with other systems, services, and devices across local or global networks. This section explores all the essential networking concepts and tools every programmer should know to build such connected applications.

We'll look at how programs send and receive data over the internet or local networks using techniques like HTTP request handling, working with APIs, and using sockets for low-level, real-time communication. You’ll also be introduced to other important topics such as:
      •	Client-server architecture
      •	Protocols (like TCP/IP, UDP, HTTP/
             HTTPS)
      •	WebSockets and real-time data 
              exchange
      •	RESTful and SOAP APIs
      •	Authentication methods (such as 
             API keys, OAuth, JWT)
      •	Network security essentials
      •	DNS and URL resolution
	•	Networking libraries and tools 
             available in your chosen 
             programming language

When you master everything mentioned in this section in your chosen programming language, you'll have a 
solid understanding of how to make your applications talk to each other, whether over the internet or 
within a private network — and how to do it securely and efficiently.


Understanding DSN
———————————
  DSN stands for Delivery Status Notification. A DSN is a type of automated message or report generated by 
an email server to inform the sender (or other systems) about the status of an email message — whether it 
was delivered, delayed, failed, or rejected.

Think of it as the email version of a “receipt” or “bounce report”.

DSN uses
—————
   •	If your email successfully reaches the 
      recipient's server: You might get a 
      delivery confirmation DSN (though 
      many servers suppress these).
   •	If the email fails (e.g., due to invalid 
      address, full mailbox, spam filter): You 
      get a bounce message — a type of 
      negative DSN.
   •	If the email is delayed and the server is 
      retrying: You may receive a delay 
      notification.

These are controlled by SMTP (Simple Mail Transfer Protocol) and can include different status codes (like 550, 421, etc.).


DSN is part of networking because:
    •	It involves server-to-server 
       communication over the internet.
    •	It uses SMTP, which is a core 
       application-layer protocol in computer 
       networking.
    •	Handling DSNs is important for building 
       robust email systems, monitoring 
       delivery, and managing bounce 
       handling — especially in apps that send 
       newsletters, alerts, or transactional 
       emails.

Don't confuse DSN (Delivery Status Notification) with DNS (Domain Name System) — which is a broader networking system used to resolve domain names to IP addresses.

 





——————————————————————
	APPLICATIONS
——————————————————————

-Create a web server that will power your application, whether its via a thrird party cloud server or your own
  local implementation. It could be using a local Linux server or Docker, or LAMP/MAMP/XAMP, or you serve your
  application on your local computer using PHP's simple built-in server.
-Basically, this covers things that you may do to launch or activate the application you have built-be it
  in production or local.
-This is foremost for you as a developer to keep a record of applications you have built using this programming language.
  The more stuff you build yourself, the more your experienced you will Become.


   The dgz CLI Tool
   =================

   Dorguzen ships with a command-line tool called `dgz`. It handles database
   migrations, seeders, code generation, the development server, queue workers,
   log inspection, and more. Every command follows the same pattern:

       php dgz <command> [options]

   `dgz` is a project-local tool — it always boots from the directory you run it
   in, reading that project's .env, config, and database connection. This is
   deliberate: it means each Dorguzen project you build is fully self-contained,
   and running `php dgz` in project A never touches project B.

   DO NOT install dgz as a global symlink pointing to a specific project. If you
   do, all projects will share one project's bootstrap, config, and database —
   which will cause confusing failures across projects.


   Running commands
   -----------------
   Always run `php dgz` from your project root (the directory containing the
   `dgz` file and your `.env`):

       cd /path/to/my-project
       php dgz migrate
       php dgz db:seed
       php dgz serve

   You must be in the project root. Running from a subdirectory will fail because
   `dgz` resolves all paths relative to its own location.


   Optional shell alias (recommended)
   ------------------------------------
   Typing `php dgz` for every command gets repetitive. You can add a shell alias
   so that bare `dgz` still delegates to the local `php dgz` — without any global
   symlink or cross-project risk:

   For zsh (the default shell on macOS):

       echo 'alias dgz="php dgz"' >> ~/.zshrc
       source ~/.zshrc

   For bash:

       echo 'alias dgz="php dgz"' >> ~/.bashrc
       source ~/.bashrc

   After adding the alias you can use the short form from any project root:

       dgz migrate
       dgz db:seed
       dgz serve

   Because `dgz` expands to `php dgz` (not to an absolute path), it always runs
   the `dgz` file in your current directory. Switch to a different project and
   the alias automatically targets that project instead — no configuration needed.

   Note: if you use a version manager like asdf, mise, or phpenv that shims the
   `php` binary, the alias works with whichever PHP version that project selects.


   Listing all available commands
   --------------------------------
   To see every command dgz supports, run:

       php dgz list

   To get help on a specific command:

       php dgz help migrate
       php dgz help db:seed


   ---


   Local Development Server (php dgz serve)
   =========================================

   Dorguzen ships with a built-in development server powered by PHP's own `-S` flag. It lets you
   run your application locally without installing Apache, Nginx, MAMP, or any other web server
   software. One command and you are live.


   Starting the server
   -------------------

   Default — starts on http://localhost:8000:
       php dgz serve

   Custom port:
       php dgz serve --port=9000

   Expose on your local network (so other devices on the same Wi-Fi can reach it):
       php dgz serve --host=0.0.0.0 --port=9000

   Once it starts you will see:

       Dorguzen development server started.
         Listening on:  http://localhost:9000
         Document root: /path/to/your/project
         Press Ctrl+C to stop.

   Open that URL in your browser and your application loads normally — routing, controllers,
   views, sessions, everything works exactly as it does under MAMP or Apache.
   Press Ctrl+C in the terminal to shut it down.


   What it is good for
   --------------------

   The built-in server is ideal for:

     • Quick local development without configuring a full web server stack
     • Working on a machine where MAMP/XAMPP is not installed
     • Sharing a prototype with a colleague on the same network (--host=0.0.0.0)
     • Automated testing pipelines that spin up a real HTTP server temporarily

   It is zero-config: no virtual hosts to set up, no DocumentRoot to point, no .conf files
   to edit. The Dorguzen serve command also sets upload_max_filesize, post_max_size, and
   memory_limit to generous values automatically, because .htaccess directives are ignored
   by PHP's built-in server.


   NOT for production — and why
   ------------------------------

   The built-in server must never be used in a live, publicly accessible environment.
   This is not a Dorguzen limitation — it is a fundamental constraint of PHP's built-in server
   itself, and PHP's own documentation states this explicitly.

   Here is why production web servers like Apache (used by MAMP) and Nginx can handle real
   traffic, but PHP's built-in server cannot:

   1. Single-threaded, single-process
      PHP's built-in server handles exactly one request at a time. While it is processing
      request A, every other incoming request waits in a queue. On a real website with even
      a handful of simultaneous visitors — or a page that loads a dozen assets (CSS, JS,
      images) in parallel — requests pile up and the server grinds to a halt.

      Apache and Nginx spawn multiple worker processes or threads and handle hundreds of
      concurrent connections simultaneously. MAMP uses Apache under the hood, which is why
      it can serve your application smoothly to a real audience.

   2. No keep-alive or connection pooling
      Modern browsers open several parallel connections to load a page faster. The built-in
      server does not support HTTP keep-alive properly, so each asset gets its own slow
      connection cycle.

   3. No static file optimisation
      Apache and Nginx serve static files (images, CSS, JS) directly from disk at OS speed,
      bypassing PHP entirely. The built-in server routes every request — including static
      assets — through PHP, which is far slower and wastes memory.

   4. No TLS / HTTPS
      The built-in server speaks plain HTTP only. Production sites require HTTPS. Apache and
      Nginx handle TLS termination natively (or via a reverse proxy like Certbot/Let's Encrypt).

   5. No process supervision
      If the built-in server crashes, it stays crashed. Production web servers integrate with
      systemd, supervisord, or similar process managers that restart them automatically.

   In short: MAMP, LAMP, XAMPP, and Nginx are engineered for reliability, concurrency, and
   security at scale. PHP's built-in server is engineered for a developer to preview their
   work quickly on their own machine. Use each tool for what it was designed for.


   Database connections
   ---------------------

   The development server just serves HTTP — it has no database bundled inside it. If your
   application needs a database (for example MySQL), you need to have MySQL running separately.
   Two straightforward options:

   Option A — Install MySQL locally via Homebrew (macOS):
       brew install mysql
       brew services start mysql

   Then set your .env credentials as usual (DB_HOST=127.0.0.1, DB_USERNAME=root, etc.).

   Option B — Run MySQL in Docker (works on macOS, Linux, Windows):
       docker run --name dgz-mysql -e MYSQL_ROOT_PASSWORD=secret -e MYSQL_DATABASE=camerooncom \
           -p 3306:3306 -d mysql:8

   Then point your .env at 127.0.0.1:3306 with the credentials you passed above.

   If you already have MAMP running, you can simply start MAMP (which runs MySQL on port 8889
   by default), keep it running in the background, and use 'php dgz serve' as the web server
   instead of MAMP's Apache. This gives you the best of both: MAMP's MySQL without needing
   MAMP's Apache.





//—————————
MTELSC
//---------————-
——————————————————————
	MIDDLEWARE
——————————————————————

          -How the DGZ Middleware System Works
          -Common Uses for Middleware
          -Creating a Middleware
          -Example: Constructor Setup
          -The boot() and handle() Relationship
          -Controlling Request Flow
          -Middleware Priority System
          -PSR Support and Jet forms validation





  How the DGZ Middleware System Works
  ------------------------------------
The DGZ middleware system provides a powerful, modular way to run code before your application launches or 
before a request is dispatched to a controller.
It works seamlessly whether you are:

    -building an app as a module (using DGZ's package system to plug into a DGZ web app), or
    -working directly within the main web application.

Middleware is where you define logic that runs in between a request being received and your controller 
being executed.



  Common Uses for Middleware
  ------------------------------
    Middleware is perfect for scenarios such as:

    -Checking if a user is authenticated before accessing a page (DGZ already provides a default middleware 
      for this).
    -Running some code before the app launches, e.g., loading a custom config or setting headers.
    -Inspecting or redirecting a request before it reaches its intended controller.
    -Pre-loading configuration data or perform setup routines unrelated to routing intents.



  Creating a Middleware
  -----------------------

To create a middleware:

  1) Create a class in a file whose name ends with Middleware.php, and place it in the /middleware/ directory.
    Example:
      
      /middleware/routeMiddleware/AuthMiddleware.php

  2) Your middleware class must implement the DGZ_MiddlewareInterface interface located in

      Dorguzen\Core\DGZ_MiddlewareInterface.php

  3) It must define the following two methods as required by the interface:

      public function boot(): array;
      public function handle($controller, $controllerShortName, $method): bool;

      boot() must return an array (it can be empty if unused). This is particularly handy to use to 
          pre-load configuration data or perform setup routines unrelated to routing intents. This 
          means, the idea of it returning an array is if you choose to let handle() call it and 
          perform some logic, but you can also place about any kind of code in there to carry out 
          some pre-request tasks before returning an empty array.   
      handle() must accept three arguments:
          -$controller → the fully qualified target controller for the request
          -$controllerShortName → the simplified controller name DGZ uses in auto-discovery routing
          -$method → the method that should handle the current request



    Example: Constructor Setup
    --------------------------
    You can include a constructor for dependency setup (but no required parameters since DGZ 
    instantiates middleware automatically). That means when you do, do not give it any required 
    parameters. Here's an example of a middleware consructor added if needed:

      public function __construct()
      {
          $this->config = container(Config::class);
          $this->users = container(Users::class);
          $this->request = container(DGZ_Request::class);
      }



      The boot() and handle() Relationship
      -------------------------------------
  DGZ gives you flexibility in how you use these methods. Both methods can be used in combination.
A common and powerful pattern — as seen in BaseMiddleware.php — is to make the boot() method return 
an associative array of “rules” where the array keys represent controller short names, and their values 
what kind of check or action (intent/pre-check) should be performed on each. 

Let's look at this example from BaseMiddleware.php:

    public function boot(): array
    {
        return [
          'account' => 'authenticated',
          'admin'   => 'authorized',
          'shop'    => 'isActiveModule',
        ];
    }

Here:

  -The key ('account') corresponds to the short name of the controller handling the request.
  -The value ('authenticated') represents the intent or type of check that should apply to that 
    controller.
  
The DGZ framework will then call your middleware's handle() method, passing the 
$controller, $controllerShortName, and $method. The handle() method will inspect the $boot array and 
determine what logic to run for each controller dynamically.
Basiically, the handle() method, can use this boot() mapping to dynamically call helper methods that 
perform the correct checks:

    public function handle(string $controller, string $controllerShortName, string $method): bool
    {
        $boot = $this->boot();

        if (!array_key_exists(strtolower($controllerShortName), $boot)) {
          return true; // Not handled by this middleware
        }

        $intent = $boot[$controllerShortName];

        switch ($intent) {
            case 'authenticated':
              if (!$this->authenticated()) {
                throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'You must be logged in.');
              }
              break;

            case 'authorized':
              if (!$this->authorised()) {
                throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'Restricted area.');
              }
              break;

            case 'isActiveModule':
              if (!$this->isActiveModule($controllerShortName)) {
                throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'Module not active.');
              }
              break;
        }

        return true;
    }


In this example:
  -If the controller short name is account, the middleware automatically checks if the user is logged in.
  -If it's admin, it verifies that the user has proper authorization.
  -If it's shop, it ensures that the target module is currently active.

This pattern gives your middleware the power to behave differently depending on which controller is being routed, 
without needing multiple separate middleware classes. It lets you define different types of validations per 
controller in one place (boot()), while keeping your logic clean and centralized in handle().
However — and this is important — you don't have to use boot() this way. It's just one creative pattern to 
show you what is possible. You can certainly design their own flow depending on the needs of your application.
It allows you to centralize several related pre-checks in one middleware class. It's especially useful for global 
middleware such as BaseMiddleware, which may need to handle multiple controller types or rule sets at once.

For example:
  -You might want to apply different authentication levels depending on the section of your app.
  -You can use the $intent values in the boot() array to trigger different logic paths dynamically.




      Controlling Request Flow
      --------------------------
  Your handle() method decides whether DGZ proceeds with or aborts the request. So, if handle():

      -Returns true → DGZ continues with the request as normal.
      -Returns false → DGZ halts the request.
      -Throws an exception → DGZ's Router catches it and displays an error view or does whatever you 
        program it to do.

  Example:

      if (!$this->authenticated()) {
        throw new DGZ_Exception(
          'Not authorized',
          DGZ_Exception::PERMISSION_DENIED,
          'You must be logged in to access this section.'
        );
      }




      Middleware Priority System
      ---------------------------

  DGZ runs all middlewares in the /middleware/ directory automatically before dispatching the request.
Each middleware can define a property named priority:

      public int $priority = 5;

Lower numbers mean higher priority (i.e., they run earlier).
If a middleware does not define a priority, DGZ assigns a default value of 10.

Example execution order:

Middleware	        Priority	        Order
CsrfMiddleware	      1	              🥇 Runs first
AuthMiddleware	      5	              🥈 Runs second
PaymentsMiddleware	  8	              🥉 Runs third
LoggerMiddleware	    10              (default)	Last

Even if you have more than 10 middlewares, the system continues to sort and execute them 
correctly — it's not limited to the number 10.




    PSR Support and Jet forms validation
    --------------------------------------
  Hybrid PSR-15 Compatible Middleware Pipeline

Dorguzen now ships with a brand-new middleware engine that finally brings:

✔ Full support for classic DGZ middleware
✔ Support for modern PSR-15 style middleware
✔ Ordered middleware execution via priority
✔ Seamless CSRF protection
✔ Integrated Jet Form validation (more on Jet Forms later)

This upgrade means Dorguzen middleware is now flexible, future-proof, and compatible with middleware written for any framework that follows the PSR-15 contract.

1. What Middleware Is in Dorguzen

Middleware is code that executes before your controllers, usually to:

Validate CSRF tokens

Authenticate users

Validate reusable Jet Forms

Modify requests

Perform logging

Run access control

Run pre-controller logic

Every middleware lives in:

/middleware/


and must end with:

*Middleware.php


Example:

CsrfPsrMiddleware.php
FormValidationMiddleware.php
AuthMiddleware.php


Dorguzen automatically loads every file ending in Middleware.php, instantiates its class, sorts them by priority, and runs them in order.

2. Two Middleware Styles Supported

Dorguzen accepts two different middleware types, and can chain them together:

A. Legacy DGZ Middleware

These middleware classes extend Dorguzen\Core\DGZ_MiddlewareInterface.

They must implement:

public function boot();
public function handle($controller, $controllerShortName, $method);

Return behaviour:

Return true → continue middleware chain

Return anything else → stop and return that value (e.g. redirect, error)

B. PSR-15 Style Middleware

These use the method:

public function process(
    PsrRequestAdapter $request,
    SimpleRequestHandler $handler
)


PSR-15 style middleware must either:

return $handler->handle($request) to continue, OR

return some response-like object / throw exception to stop

Dorguzen exposes two internal helper classes to make this possible:

Dorguzen\Core\Psr\PsrRequestAdapter
Dorguzen\Core\Psr\SimpleRequestHandler


These mimic PSR-7 / PSR-15 interfaces just enough to make PSR-style middleware functional without requiring external libraries.

3. How Dorguzen Executes Middleware

The heart of this system is the method:

DGZ_Router::runMiddleware()

Step-by-step:
Step 1 — Auto-load middleware classes

Dorguzen scans:

middleware/*.php


Loads each class and stores instances in an array.

Step 2 — Sort middleware by priority

Each middleware may optionally define:

public int $priority = 5;


Lower = runs earlier (e.g., CSRF has priority 1).

Default priority = 10.

Step 3 — Build a middleware pipeline

Dorguzen builds a chain of closures—a “pipeline”—from last to first.

Each middleware becomes a callable:

fn(PsrRequestAdapter $req): mixed


This means all middleware (DGZ or PSR) must look identical to the pipeline.

Step 4 — Adapt Legacy DGZ Middleware

If middleware implements DGZ_MiddlewareInterface, Dorguzen wraps it:

$mw->boot();
$result = $mw->handle($controller, $controllerShortName, $method);


If it returns true, the pipeline continues.

If it returns a value (redirect/response), pipeline stops.

Step 5 — Adapt PSR-15 Middleware

If middleware contains process():

$handler = new SimpleRequestHandler($next);
return $mw->process($psrRequest, $handler);


This mimics official PSR-15 behaviour.

Step 6 — Fallback for weird shapes

If nothing matches, Dorguzen simply skips the middleware—but logs nothing.

Step 7 — Kick the pipeline

The router creates a new:

PsrRequestAdapter(DGZ_Request)


and runs:

$next($psrRequest)


If final middleware returns:

true or null → routing proceeds

anything else → routing stops and that return is used

4. CSRF Middleware (example)

Dorguzen includes CSRF protection implemented as:

PSR-style middleware using process().

It receives the request from PsrRequestAdapter, reads headers/body, validates the token, then either:

returns $handler->handle($request) → allow request

throws/returns response → stop pipeline

Dorguzen's DGZ_Form (used by Jet Forms) integrates automatically with this, so developers never need to manually add CSRF fields.

5. Form Validation Middleware
(What Powers Jet Forms)

This is a DGZ-style middleware using handle().

It:

Detects submitted reusable Jet Forms via hidden _form_name

Resolves the form using JetFormsRegistry

Fills the form with input

Runs the form’s validation rules

Throws ValidationException on failure
Router catches this → sets $_SESSION['old_input'] and validation_errors

On success → stores validated data and continues

Jet Forms “just work” because of this middleware.
(Documentation for Jet Forms comes next.)

6. Writing Custom Middleware

Here are examples in both supported styles.

A. Legacy DGZ Middleware Example
class AuthMiddleware implements DGZ_MiddlewareInterface
{
    public int $priority = 3;

    public function boot() {}

    public function handle($controller, $controllerShortName, $method): bool
    {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
            return false;
        }

        return true;
    }
}

B. PSR-15 Style Middleware Example
class ExamplePsrMiddleware
{
    public int $priority = 8;

    public function process($request, $handler)
    {
        if ($request->getHeader('X-Block-Me') === '1') {
            return 'blocked'; // stop pipeline
        }

        return $handler->handle($request);
    }
}

7. Middleware Execution Order

Example:

priority 1 → CsrfPsrMiddleware  
priority 3 → AuthMiddleware  
priority 5 → FormValidationMiddleware  
priority 10 → Any other middleware


Earlier = more critical.

8. Why This Hybrid Approach Is Powerful

Dorguzen is now able to:

Run middleware from old DGZ projects

Run full PSR-15 middleware from external tutorials/libraries

Support modern middleware chains without requiring PSR-7/PSR-15 libraries

Allow deep customization of request pipelines

This means your framework gains modern architecture without losing its identity.


9. Jet Form Support (Preview)

Reusable forms rely entirely on middleware:

FormValidationMiddleware handles validation

CSRF middleware handles CSRF tokens

Jet Forms remain ultra-lightweight

Developers write only the form rules, not the plumbing

Consult the Forms and Email section for the full understanding of Jet Forms chapter.




      Summary
      --------
Here are some take-away points to master DGZ's middleware system:

  -All middlewares live in /middleware/
  -They must implement DGZ_MiddlewareInterface
  -They can define boot() and handle()
  -They may use a constructor for dependency setup  
  -handle() determines if DGZ continues or aborts
  -Priority numbers control execution order
  -Exceptions thrown from middleware are caught and handled by the router. 






——————————————————————
	TEMPLATING
——————————————————————

-This is about the available templating 
  engine(s) available for your programming 
  language, and how it works with them.




——————————————————————
	EVENT HANDLING
——————————————————————

                        -Events handling in Dorguzen
                          -Event vs Job — The Conceptual Difference
                          -What is an Event?
                          -What is a Job?
                          -When Does an Event Become a Job?
                          -Creating a Custom Event (ProductPurchased Example)
                          -The Jobs processing Lifecycle
                          -Writing Custom Job Classes in Dorguzen
                          -Built-in Dorguzen Core Events
                            -Event classes as data containers — how hydration works
                            -Why built-in events matter
                            -The five core events
                              -UserRegistered
                              -UserLoggedIn
                              -UserLoggedOut
                              -UserSubscribed
                              -ContactFormSubmitted
                            -Built-in listeners shipped with each event
                            -Adding your own listener to a core event
                            -Logging inside listeners — use DGZ_Logger
                            -Making a listener asynchronous (ShouldQueue)
                        -SLACK NOTIFICATIONS
                            -What are Slack Incoming Webhooks?
                            -Create a Slack Incoming Webhook
                            -Configuration
                            -Sending a message
                            -Three ways to trigger a Slack notification
                                -Option A: Call DGZ_Slack::send() directly
                                -Option B: Dispatch a queued Job
                                -Option C: A Listener on an existing Event
                            -Common use cases
                            -Error handling and resilience
                            -Advanced payloads — Slack Block Kit

-This tells you how your programming 
  language helps you respond to events. 
  This brings to mind design patterns like the 
  Subscriber pattern (pub-sub), the 
  dispatching of jobs based on the triggering 
  of certain events and the 
  consumption of such, within your 
  application.
-Note here that the Command topic often  
  has a great role to play here.



  Events handling in Dorguzen
  ----------------------------
    -1) Event vs Job — The Conceptual Difference
    -----------------------

This is the most important distinction.

🔹 What is an Event?

An Event represents something that has happened in your application.

Examples:

  UserRegistered
  OrderPlaced
  PasswordResetRequested

An event answers the question: “What just happened?”
It does not describe work to be done. It simply describes an occurrence.

🔹 What is a Job?

A Job represents work that needs to be processed.

For example:

  Send an email
  Resize an image
  Generate a report
  Process a payment

A job answers the question: “What needs to be executed?”



  🔹 When Does an Event Become a Job?
  -----------------------

An event never becomes a job by itself. Instead, an event is dispatched.
Its listeners are resolved, and if a listener implements ShouldQueue, that listener is wrapped into a QueuedJob

The QueuedJob is then pushed into a queue. So the flow of the transformation goes like this:

  Event
    ↓
  Listener
    ↓ (if implements ShouldQueue)
  QueuedJob

Only the listener, not the event itself, becomes a queued job.
That distinction is very important.




-2) Creating a Custom Event (Dummy Example)
-----------------------

Let's build a completely custom event from scratch. We will use
ProductPurchased as the example — a domain event that fires whenever a
user completes a purchase.

(Note: Dorguzen also ships with core events such as UserRegistered,
UserLoggedIn, etc. that fire automatically. This section shows how to
create your own on top of those.)

You can scaffold the files with the CLI:

      php dgz make:event  ProductPurchased
      php dgz make:listener NotifyAdminOfPurchase
      php dgz make:listener SendPurchaseReceiptEmail

Then fill them in:


Step 1 — Create the Event

File: src/events/ProductPurchased.php

      namespace Dorguzen\Events;

      class ProductPurchased
      {
          public function __construct(
              public int    $userId,
              public string $userEmail,
              public int    $productId,
              public string $productName,
              public float  $amount,
          ) {}
      }

Simple. It is just a data container — no methods, just the facts about
what happened.


Step 2 — Create Listeners

Listener A — synchronous (runs immediately, before the response is sent)

File: src/listeners/NotifyAdminOfPurchase.php

      namespace Dorguzen\Listeners;

      use Dorguzen\Events\ProductPurchased;
      use Dorguzen\Core\DGZ_Logger;

      class NotifyAdminOfPurchase
      {
          public function handle(ProductPurchased $event): void
          {
              DGZ_Logger::info('Purchase completed', [
                  'user_id'      => $event->userId,
                  'product_id'   => $event->productId,
                  'product_name' => $event->productName,
                  'amount'       => $event->amount,
              ]);
              // could also send a Slack notification here via DGZ_Slack::send()
          }
      }

This runs immediately and completes before the HTTP response is returned
to the user.

Listener B — queued (runs in the background via the queue worker)

File: src/listeners/SendPurchaseReceiptEmail.php

      namespace Dorguzen\Listeners;

      use Dorguzen\Core\Events\ShouldQueue;
      use Dorguzen\Events\ProductPurchased;
      use Dorguzen\Core\DGZ_Messenger;

      class SendPurchaseReceiptEmail implements ShouldQueue
      {
          public function handle(ProductPurchased $event): void
          {
              $messenger = new DGZ_Messenger();
              $messenger->sendEmail(
                  $event->userEmail,
                  "Your purchase of {$event->productName}",
                  "Thank you! Your payment of {$event->amount} was received."
              );
          }
      }

Because it implements ShouldQueue, this listener is not called inline.
Instead, EventDispatcher hands it to QueueManager, which stores the job
and returns immediately. The queue worker processes it in the background.


Step 3 — Register in configs/events.php

      return [
          \Dorguzen\Events\ProductPurchased::class => [
              \Dorguzen\Listeners\NotifyAdminOfPurchase::class,    // runs now
              \Dorguzen\Listeners\SendPurchaseReceiptEmail::class,  // runs in queue
          ],
      ];

You can add as many listeners as you like. They fire in the order listed.
Synchronous listeners run first, then queued ones are handed off.


Step 4 — Dispatch the Event

Inside your controller, after a successful purchase:

      event(new ProductPurchased(
          userId:      (int) $user->users_id,
          userEmail:   $user->users_email,
          productId:   (int) $product['product_id'],
          productName: $product['product_name'],
          amount:      (float) $order['total_amount'],
      ));



-3) What Happens Internally
-----------------------

Let's trace the exact flow.

Step A — event() Helper

The helper:

    use Dorguzen\Core\Events\EventService;

     /**
     * It is recommended to use event() to dispatch Events, as Events 
     * are meant to pass through the EventService class pipeline. 
     * 
     * Events are not candidates for the queueing system, though an Event may be 
     * marked for queueing by making its listener implement the ShouldQueue interface
     * (core/events/ShouldQueue.php), in which case, event() will detect that internally 
     * and hand the Event over to the queue system to be dispatched as a job.
     * 
     * Example usage:
     *  event(new TestEvent('hello'));
     */
    if (!function_exists('event')) {
        function event(object $event): void
        {
            container(EventService::class)->dispatch($event);
        }
    }

It sends the event into the system.

Step B — EventDispatcher

The EventService (in core/events/EventService.php) class sends the event into the system by passing the 
raised event to the dispatch() method of the EventDispatcher class.
This EventDispatcher->dispatch() resolves all registered listeners of that event from config,
loops through them and checks 

  if a $listener is an instanceof ShouldQueue, and places it in a queue if it does, otherwise it runs the 
listener immediately by calling its handle() method which all listener classes must have. 
      

Step C — Immediate Listener
      $listener->handle($event);


Step D — Queued listener
  If the $listener is an instance (implements) ShouldQueue, then the listener will be placed in a queue 
instead of being run:

      $job = new QueuedJob($listenerClass, $event);
      $queueManager->push($job);

Now we are officially in Job territory.

The event has triggered a job.

Once more, in summary; EventService is the Dorguzen entry point for events. The flow goes like this:

    -EventService dispatches a service to EventDispatcher, 
    -EventDispatcher uses ListenerResolver to resolve listeners from the configuration (the central glue),
    -EventDispatcher after resolving a listener class, checks if the listener implements ShouldQueue.
      If the listener does not implement ShouldQueue, it runs that listener directly by calling the 
      handle() method of the listener to handle the event.
      If the listener implements ShouldQueue, then it knows that the listener needs to become a queued job,
      so it packages the event listener into a QueuedListener. See this as a way of standardising the format 
      of the listener as a queued object, for easy consumption later. It then passes this QueuedListener
      to the push() method of QueueManager, which uses the currently active queue type 
      (based on the 'queue_driver' setting ) to queue the event. 
      This is how an event reaches the queue system, and the EventDispatcher class is the bridge. So, again, 
      only events whose listeners implement ShouldQueue will hit the queue system. Jobs, naturally, unlike 
      events, only use the queue system. 




-4) The dispatch() Helper (For Jobs)
-----------------------

Beside the event() helper, Dorguzen also has dispatch():

    dispatch($job);

This is different from event().

🔹 event() is used for domain events.

It may:
  Run immediately
  Or create queued jobs (if listeners implement ShouldQueue)

🔹 dispatch() on the otherhand is used when you explicitly want to queue a job.

Example:

      dispatch(new GenerateMonthlyReport($month));

This bypasses the Event layer entirely and goes straight to the:

  QueueManager → which uses the active queue driver set in config ('sync' or 'db') 
    to know how to queue the job for processing.
Here is an example of what the queue setting in your .env (config) file looks like:

  # Queueing. Options: sync, db
  # If the driver is db, you need an existing worker loop (CLI command), which uses the jobs table
  QUEUE_DRIVER=db


When To Use Each?
-----------------------
  Use Case	                                Helper
  Something happened in your domain	        event()
  You want to queue specific work	          dispatch()


Important Distinction
-----------------------
Not all events become jobs.
Only listeners implementing ShouldQueue do.

But all jobs dispatched with dispatch() are jobs immediately.




-5) How Database Queue Works
-----------------------

When using DatabaseQueue driver:

'queue.default' => 'database'

When a Job is Pushed

      DatabaseQueue->push($job);

It inserts into the dgz_jobs table:

      INSERT INTO dgz_jobs (
          queue,
          payload,
          attempts,
          max_attempts,
          available_at,
          created_at
      )
      VALUES (...)

  Where the payload value will be typically the listener class serialized e.g. 
  
      serialize($job)

  as well as other meta data about the event

  attempts will be like 3 or however maximum number of times you would like your queue worker to attempt to 
  consume the job before permanently marking it as failed.



-6) How Jobs Are Processed (Worker)
-----------------------

Dorguzen has a queue worker daemon which you can fire up to run permanently, waiting to consume jobs. Start it using the following command:

      php dgz queue:work

Internally; this command grabs all existing jobs (event listener classes) from the dgz_jobs table, one by one,  
unserializes them, and consumes them by running their handle() method which all event listener classes must have. 

      while (true) {
          $job = $queue->pop();

          if ($job) {
              $job->handle();
          }
      }




-7) Handling Race Conditions (Very Important)
-----------------------

This is critical for production systems.
Because multiple workers may be running at the same time, the Dorguzen queue worker in ordr to prevent 
workers processing the same job more than once; it implements some atomic DB operations that involve 
locking of the database row to mark that job as already being process so other queue workers will skip 
it, and then marking the job as completed when its done by updating a status flag on the table. The field
that is used in this locking is the 'reserved_at' field on the same 'dgz_jobs' table.

Typical safe pattern: Before any queue worker grabd a job to process, it makes sure it only grabs those 
whose 'reserved_at' field has a value of NULL. 

    SELECT * FROM jobs
    WHERE reserved_at IS NULL
    ORDER BY id
    LIMIT 1
    FOR UPDATE

Then when it is done processing each job, it sets the flag by updating that same 'reserved_at' field to the 
current timestamp e.g:

    UPDATE jobs
    SET reserved_at = NOW()
    WHERE id = ?

This ensures:

✔ Only one worker reserves a job
✔ Other workers skip it
✔ No double processing

This is called pessimistic locking.



-8) SyncQueue vs DatabaseQueue
-----------------------

Let us look at the characteristics of the two types of queues Dorguzen uses; SyncQueue and DatabaseQueue.

SyncQueue

  -No DB
  -No workers
  -Immediate execution
  -Useful for development


DatabaseQueue

  -Persistent storage
  -Background workers
  -Safe concurrent processing
  -Retry capability
  -Scalable


-9) Full Lifecycle Summary
-----------------------

    Developer calls event()
            ↓
    EventDispatcher
            ↓
    Resolve listeners
            ↓
    If NOT ShouldQueue:
            → handle immediately

    If ShouldQueue:
            ↓
    Create QueuedJob
            ↓
    QueueManager
            ↓
    SyncQueue OR DatabaseQueue
            ↓
    (If database)
    Stored in jobs table
            ↓
    Queue Worker
            ↓
    pop()
            ↓
    handle()



-10)  Why This Design Is Powerful
-----------------------

✔ Domain logic stays clean
✔ Async behavior is opt-in via interface
✔ Drivers are swappable
✔ Safe concurrent job processing
✔ Clear separation of responsibilities


Final Developer take-away points

If I'm building on Dorguzen:  
  I create events to describe domain occurrences
  I attach listeners to react to those occurrences
  I implement ShouldQueue when work should be async
  I use dispatch() when I want direct job execution
  I rely on Dorguzen to:
  Serialize jobs
  Store them
  Prevent race conditions
  Process them safely via workers





    The Jobs processing lifecycle
    ------------------------------
    The Dorguzen Job Processing Lifecycle (Deep Explanation)

Before anything else, ensure your application has these two tables:

dgz_jobs
dgz_failed_jobs

Run migrations if they do not exist. Dorguzen ships with the migration files for these tables, so running your migrations will set them up for you.

These two tables are the foundation of asynchronous processing in Dorguzen.


Events vs Jobs (Re-stated for Clarity)
--------------------------

This distinction is extremely important.

Events

Live in: src/events/

Represent something that happened. They may or may not be queued. They only become queued 
if their listener implements ShouldQueue.

Jobs

Live in: src/jobs/

They always use the queue system. They are explicitly dispatched using dispatch().
They never run outside the queue system.

So remember:

All Jobs are queued.
Not all Events are queued.

An Event only enters the queue system if its listener implements:
  implement Dorguzen\Core\Events\ShouldQueue;


How an Event Reaches the Queue System
--------------------------

Let's repeat this carefully. A developer calls:

    event(new UserRegistered($user));

EventService passes it to EventDispatcher.
EventDispatcher resolves listeners using ListenerResolver.

For each listener:

  -if it does NOT implement ShouldQueue:
    run its handle() method immediately

  -If it DOES implement ShouldQueue:
    it is wrapped into a QueuedListener
    and passed to QueueManager->push()

This is the exact bridge between Events and the Queue system.

Only here does the Event system hand over control to the Queue.



The Queue System (Deep Internal Explanation)
--------------------------

Now we move fully into the Queue system.

The queue system consists of:

QueueManager
QueuedJob
Drivers:
    QueueInterface
    SyncQueue
    DatabaseQueue

The active driver is controlled by the configuration setting:

    'queue_driver' 
    
which can either be 

    'sync' or 'db'


DatabaseQueue — The Asynchronous Engine
--------------------------

This is where things become serious. DatabaseQueue stores jobs inside:

  the 'dgz_jobs' table

Here is the structure of the 'dgz_jobs' table:

      id
      queue
      payload
      attempts
      max_attempts
      reserved_at
      available_at
      failed_at
      created_at
      reserved_at


When a Job Is Pushed
----------------------

That means the job is queued; it is initiated from Dorguzen\Core\Queues\QueueManager, which checks for 
the active queue driver in configs

    'queue_driver' which can either be SyncQueue or DatabaseQueue, and the signature of the push() method 
    in both drivers looks like this:

      public function push(object $job, ?int $delaySeconds = null)

As mentioned, if the active queue driver is DatabaseQueue, it inserts into the database:

      $this->db->insert('dgz_jobs', [
          'queue'        => 'default',
          'payload'      => serialize($job),
          'attempts'     => 0,
          'max_attempts' => 3,
          'available_at' => date(...),
          'created_at'   => date(...),
      ]);

Important details:
  -payload is serialized.
  -attempts starts at 0. The 'attempts' field represents how many times (tries) workers tried to process the job
  -max_attempts defaults to 3.
  -available_at controls when the job becomes eligible for processing.
  -reserved_at is NULL initially.



The Worker Process
-------------------

The worker runs in CLI:

      php dgz queue:work

Internally, it is a daemon that runs continuously once started:

while (true) {
    $job = $queue->pop();

    if ($job) {
        try {
            $job->handle();
            $queue->acknowledge($job);
        } catch (Throwable $e) {
            $queue->release($job);
        }
    }
}

This is the full lifecycle.

pop() — is how a driver safely claims a job from the database.
This is the most important part of your entire system.

Let's walk through it carefully.

Step 1 — Find Available Job
  SELECT * FROM dgz_jobs
  WHERE reserved_at IS NULL
  AND available_at <= NOW()
  ORDER BY id ASC
  LIMIT 1

  This SQL query means:

    -get a job that is not already reserved (reserved_at IS NULL). This is the locking mechanism that 
        ensures that tow queue workers do not process thesame job.
    -The job is due for processing, and not delayed (<= NOW())
    -get the oldest job first in FIFO style (ORDER BY id ASC)

  NULL will be returned if none found.

Step 2 — Attempt to Reserve It (Race Condition Protection)
  Now comes the critical part. After a job is processed, this is done:

  UPDATE dgz_jobs
  SET reserved_at = NOW(),
      attempts = attempts + 1
  WHERE id = ?
  AND reserved_at IS NULL

  Notice this:

  AND reserved_at IS NULL

This is optimistic locking. Why? Because multiple workers may:

  Read the same job, and therefore try to reserve it simultaneously. This lock mechanism ensures 
  that only ONE worker will successfully update the row, preventing double execution.
  The other queue workers running will skip a row that's already taken by another worker:

    -Affecting 0 rows
    -Returning null
    -Will skip processing of that row

  This is your race-condition safety mechanism.



Attempts and Retries Explained
----------------------------

When a job is first inserted:

      attempts = 0
      max_attempts = 3

When a worker reserves it, it increments the attempts number by 1: 

      attempts = attempts + 1

So on first execution attempt, 'attempts' will be equal to 1:

    attempts = 1



What Happens If Job Succeeds?
---------------------------

The worker marks the job as done. It does so by running this line:

      $queue->acknowledge($job);
      

Which basically removes the Job from the 'dgz_jobs' datbase table, effectively removing it from the queue.

    DELETE FROM dgz_jobs WHERE id = ?




What Happens If Job Fails?
---------------------------

If an exception is thrown:

      catch (Throwable $e) {
          $queue->release($job);
      }

Now we enter retry logic.


release() — Retry Logic
---------------------------
When grabbing jobs to process, the system checks if the number of tries has not exceeded the maximum number 
of attempts.

      if ($job->attempts >= $job->maxAttempts)

If the attempts have reached the max:

    -the job is moved to the 'dgz_failed_jobs' table.
    -and then deleted from 'dgz_jobs'

This is poison-job protection.

If the attempts have not reached the max, it is marked as taken (reserved_at), then processed, and then marked as 
released again (reserved_at = NULL), with its 'attempts' value inclremented by 1:

      UPDATE dgz_jobs
      SET reserved_at = NULL,
          attempts = ?,
          available_at = future_time
      WHERE id = ?

What happens here?

reserved_at becomes NULL → job becomes available again
attempts stays incremented
available_at is pushed into future (default 5 seconds)

This prevents immediate hammering retries.



Fail() — Permanent Failure Handling
---------------------------

When job has exceeded its max attempts at the processing was unsuccessful, it is saved into 'dgz_failed_jobs'
and removed from 'dgz_jobs':

      $this->db->insert('dgz_failed_jobs', [
          'queue' => ...,
          'payload' => serialize(...),
          'exception' => message,
          'exception_trace' => trace,
          'attempts' => ...,
          'failed_at' => ...
      ]);

      And:

      DELETE FROM dgz_jobs WHERE id = ?

So:

    ✔ Job is removed from active queue
    ✔ Failure is recorded permanently
    ✔ Debugging information preserved



How the 'available_at' field delays execution
---------------------------
This field has two roles:
    -Delay execution when first pushed.
    -Delay retries.

A job will not be picked up unless:
    -available_at is less than, or equal to the current timestamp (available_at <= NOW())

This is how Dorguzen implements:
    -Delayed jobs
    -Retry backoff
    -Scheduled execution timing



The 'reserved_at' — Locking Mechanism
---------------------------

This field is critical. As a reminder, here are things to know:
  When its value is NULL, it means:
    -The Job is free

  When its value is the current timestamp, it means:
    -The Job is currently being processed

  Workers only pick jobs where:

    reserved_at IS NULL

This prevents duplicate execution.



Summary of Full Lifecycle
---------------------------

Let's describe the full journey.

Step 1 — Job Inserted
    attempts = 0
    reserved_at = NULL
    available_at = NOW

Step 2 — Worker Finds Job

  Queue workers check if:

    reserved_at IS NULL
    available_at <= NOW

Step 3 — Worker Claims matching Job, runs it and updates it like so:

    reserved_at = NOW
    attempts = attempts + 1

    Only one worker succeeds.

Step 4 — Job Runs

    If it succeeds:
      it does acknowledge() (which basically means it deletes if from the queue)
    If it fails 
      it does a release() from the queue (which basically means it inserts it into the 'dgz_failed_jobs' table 
      and deletes it from the 'dgz_jobs' table)

Step 5 — Retry or Fail Permanently

    If attempts < max_attempts - repeat the cycle:

    reserved_at = NULL
    available_at = NOW + delay

    If attempts >= max_attempts:

    INSERT INTO dgz_failed_jobs
    DELETE FROM dgz_jobs


Poison Job Protection
-----------------------
A poison job is a job that will never pass processing, either because there could be an error in the process. It 
will therefore wear on your system to keep using resources in trying to process it endlessly.
The maximum attempts limitation protects your system from infinite loops.

Without this:
  -A broken job could retry forever
  -your queue will be overloaded
  -It will cause CPU exhaustion

That is why Dorguzen stops the processing after max_attempts. You can adjust this attempt figure.



Summary of the features of the Dorguzen queue system
---------------------------

It is driver-based
It supports sync and database
Protects against race conditions
Supports retries
Supports delayed jobs
Records permanent failures
Safely deletes successful jobs
And most importantly; the developer does not need to manage any of this manually.

They simply run:

dispatch(new ProcessReport());
or queue(new ProcessReport());

Or:

event(new UserRegistered());

And Dorguzen handles:

  -Serialization
  -Locking
  -Retrying
  -Backoff
  -Failure storage
  -Concurrency protection etc





    Writing Custom Job Classes in Dorguzen
    -----------------------------------------

Remember:

Jobs live in src/jobs/

Jobs ALWAYS use the queue system.

Jobs are dispatched using the dispatch() helper.

Jobs must contain a handle() method.

A Job is simply a class whose handle() method contains the logic you want to run asynchronously.

Example 1 — A Job That Runs Immediately

Let's create:

src/jobs/LogSomethingJob.php
namespace Dorguzen\Jobs;

class LogSomethingJob
{
    public function handle(): void
    {
        file_put_contents(
            storage_path('logs/test.log'),
            "Job ran at: " . date('Y-m-d H:i:s') . PHP_EOL,
            FILE_APPEND
        );

        echo "LogSomethingJob executed successfully.\n";
    }
}

Now dispatch it:

dispatch(new \Dorguzen\Jobs\LogSomethingJob());
What Happens Internally?

If your queue_driver is:

'queue_driver' => 'sync'

→ The job runs immediately
→ handle() executes instantly
→ No database entry created

If your queue_driver is:

'queue_driver' => 'db'

→ Job is inserted into dgz_jobs
→ Worker must process it

Example 2 — A Job That Runs in 5 Minutes
namespace Dorguzen\Jobs;

class DelayedEmailJob
{
    public function handle(): void
    {
        echo "Delayed job executed at: " . date('Y-m-d H:i:s') . "\n";
    }
}

Dispatch it with delay:

dispatch(new \Dorguzen\Jobs\DelayedEmailJob(), 300);

(300 seconds = 5 minutes)

What Happens?

DatabaseQueue->push() sets:

'available_at' => NOW + 300 seconds

The worker will ignore this job until:

available_at <= current time

Even if worker is running, it will skip it until eligible.

Example 3 — A Job That Fails (To Test Retries)
namespace Dorguzen\Jobs;

    class FailingJob
    {
        public function handle(): void
        {
            echo "FailingJob running...\n";

            throw new \Exception("This job failed intentionally.");
        }
    }

Dispatch:

dispatch(new \Dorguzen\Jobs\FailingJob());
What Happens When Worker Runs?

Let’s assume:

max_attempts = 3
Attempt 1:

Worker reserves job

attempts becomes 1

handle() throws exception

release() called

reserved_at set to NULL

available_at set to NOW + 5 seconds

Attempt 2:

attempts becomes 2

fails again

delayed again

Attempt 3:

attempts becomes 3

fails again

release() sees attempts >= max_attempts

fail() is triggered

Now:

✔ Job inserted into dgz_failed_jobs
✔ Job removed from dgz_jobs
✔ Exception message + trace stored

This is poison job protection.

Running the Queue Worker

To process queued jobs:

php dgz queue:work

This starts a long-running CLI process.

What Happens When You Run queue:work?

Internally, the worker:

Continuously calls pop()

If a job is returned:

Runs $job->handle()

If success → acknowledge()

If failure → release()

If no job:

Prints: "No jobs in the queue"

Sleeps briefly

Loops again

What Developers Will See in Testing

If they dispatch:

dispatch(new FailingJob());

Then run:

php dgz queue:work

They will see:

FailingJob running...
FailingJob running...
FailingJob running...

Then it disappears from active queue and appears in:

dgz_failed_jobs
Queue Workers — Deep Production Explanation

Now we document how workers behave in real environments.

This is important.

1️⃣ Queue Workers Are Long-Running Processes

When you run:

php dgz queue:work

It does NOT exit.

It runs continuously.

It is meant to:

Stay alive

Process jobs as they arrive

Keep looping forever

In production, this is usually managed by:

Supervisor (Linux)

Systemd

Docker container

PM2

Kubernetes

2️⃣ Why It Does Not Drain Resources

Your worker does something important:

When no job is available:

sleep(1);

(or similar)

This means:

CPU usage drops to near zero

It waits calmly

It wakes up periodically to check again

It does NOT busy-loop aggressively.

That makes it safe for long-term use.

3️⃣ Real-Life Production Setup

In production, you typically:

Set queue_driver to 'db'

Start worker in background

Let it run forever

Example using Supervisor:

[program:dorguzen-worker]
command=php /path/to/project/dgz queue:work
autostart=true
autorestart=true

Now:

If worker crashes → it restarts automatically

If server reboots → worker starts again

4️⃣ Graceful Termination

To stop the worker manually:

Press:

CTRL + C

This terminates the CLI process.

In production:

supervisorctl stop dorguzen-worker

Or:

kill <process_id>
5️⃣ Multiple Workers

Because of optimistic locking:

WHERE reserved_at IS NULL

You can safely run:

php dgz queue:work
php dgz queue:work
php dgz queue:work

Multiple workers can run simultaneously.

They will not process the same job twice.

This allows horizontal scaling.

6️⃣ Why Workers Must Be Long Running

Spawning a new PHP process per job would:

Be slow

Be inefficient

Increase memory churn

Instead:

Dorguzen keeps one process alive and:

Loads framework once

Reuses memory

Loops efficiently

This is professional-grade architecture.

Full Mental Model for Developers

When a developer writes:

dispatch(new SendInvoiceJob($invoice));

They should understand:

Job serialized

Inserted into dgz_jobs

Worker finds it

Worker locks it

Worker executes it

On success → deleted

On failure → retried

On max failure → moved to dgz_failed_jobs

All automatically.

Final Note for Documentation

You can confidently state:

Dorguzen provides a production-ready queue system with:

Delayed jobs

Retry logic

Failure logging

Concurrency safety

Poison job protection

Long-running workers

Horizontal scalability

And the developer only needs to:

dispatch(new MyJob());


  BUILT-IN DORGUZEN CORE EVENTS
  ================================

  Event classes as data containers — how hydration works
  --------------------------------------------------------
  If you open any event class (e.g. src/events/UserRegistered.php) you will
  notice it has no methods. It is a plain PHP class whose only job is to
  hold data. Think of it as a sealed envelope — it carries information about
  what just happened, and every listener that receives it can read that
  information.

  The envelope is filled (hydrated) at the exact moment the event is
  dispatched. In Dorguzen, that happens inside a controller like this:

      $activationUrl = $this->config->getHomePage() . 'auth/verifyEmail?em=' . $activationCode;
      event(new UserRegistered((int) $saved, $username, $email, $firstname, $activationCode, $activationUrl));

  Breaking that line down:

    new UserRegistered(...)   Creates the event object and passes real
                              values into its constructor. PHP's constructor
                              property promotion assigns them directly to
                              the public properties ($userId, $username,
                              $email, $firstname, $activationCode,
                              $activationUrl). This is the hydration step —
                              the object is now fully populated.

    event(...)                Passes the hydrated object into the
                              EventService pipeline. EventService resolves
                              all listeners registered for this event in
                              configs/events.php and calls handle() on each
                              one, passing the same event object in.

  By the time handle() runs in any listener, the event is already loaded:

      public function handle(UserRegistered $event): void
      {
          // $event->userId, $event->username, $event->email,
          // $event->firstname are all available and populated.
          DGZ_Logger::info('User registered', [
              'user_id'  => $event->userId,
              'username' => $event->username,
          ]);
      }

  The full flow from dispatch to listener:

      new UserRegistered($id, $username, $email, $firstname)
              ↓  hydrated here — constructor properties set
          event()  helper
              ↓
          EventService → EventDispatcher
              ↓  resolves listeners from configs/events.php
          LogUserRegistration::handle($event)   ← same fully-loaded object
          SendWelcomeEmail::handle($event)       ← same fully-loaded object

  Every listener in the chain receives the same object. None of them need
  to do a database lookup to find the user's name or email — it is already
  sitting in the event properties, placed there at dispatch time.

  This is why event constructors are deliberate about what data they carry:
  too little and listeners have to query the database themselves (slow,
  couples listeners to models); too much and the event becomes a dumping
  ground. The goal is to carry exactly the data that listeners are most
  likely to need.


  Why built-in events matter
  ---------------------------
  A framework that ships with no events forces every developer to wire up
  the same lifecycle hooks from scratch on every project. Dorguzen ships
  with a set of core events that fire automatically at the most common and
  important moments in any web application — user registration, login,
  logout, newsletter subscription, and contact form submission.

  This means that from day one, a Dorguzen developer can react to any of
  these moments simply by registering a listener in configs/events.php.
  No changes to controllers, no boilerplate — just add the listener and it
  runs automatically.


  The five core events
  ---------------------
  All core events live in src/events/ and are dispatched automatically by
  the framework. All carry only the data that is directly relevant to the
  occurrence — no database lookups needed inside listeners.

  UserRegistered                          src/events/UserRegistered.php
  ---------------
  Fired after a new user account is successfully created, whether via the
  web registration form (AuthController::doRegis()) or the API
  (AuthApiController::register()).

  Payload:
      $event->userId          int     The new user's database ID
      $event->username        string  Their chosen username
      $event->email           string  Their email address
      $event->firstname       string  Their first name
      $event->activationCode  string  The MD5 hash stored in users_eactivationcode
                                      (empty string if your app does not use email
                                      verification)
      $event->activationUrl   string  The full verification URL, e.g.
                                      https://yourapp.com/auth/verifyEmail?em=<code>
                                      Pre-built by the controller so listeners do not
                                      need to know how URLs are constructed.
                                      (empty string if your app does not use email
                                      verification)

  The controller builds $activationUrl before dispatching:

      $activationUrl = $this->config->getHomePage() . 'auth/verifyEmail?em=' . $activationCode;
      event(new UserRegistered((int) $saved, $username, $email, $firstname, $activationCode, $activationUrl));

  The built-in SendWelcomeEmail listener reads $event->activationUrl and sends
  the activation email. This keeps the email-sending logic out of the controller
  entirely — doRegis() fires the event and moves on.

  Example uses: send an account activation email (→ SendWelcomeEmail), log the
  registration (→ LogUserRegistration), notify a CRM.


  UserLoggedIn                            src/events/UserLoggedIn.php
  -------------
  Fired after a user successfully authenticates, whether via the web login
  form (AuthController::doLogin()) or the API (AuthApiController::login()).

  Payload:
      $event->userId      int     The user's database ID
      $event->username    string  Their username
      $event->userType    string  Their account type (member, admin, etc.)

  Example uses: log login activity, detect logins from new locations,
  update a "last_login" timestamp.


  UserLoggedOut                           src/events/UserLoggedOut.php
  --------------
  Fired just before the user's session is destroyed in
  AuthController::logout(). It is dispatched before session_destroy() so
  that the user's identity is still available to listeners.

  Payload:
      $event->userId      int     The user's database ID
      $event->username    string  Their username

  Example uses: log the logout for an audit trail, record session duration,
  invalidate server-side caches tied to that user.


  UserSubscribed                          src/events/UserSubscribed.php
  ---------------
  Fired after a visitor successfully subscribes to the newsletter via
  FeedbackController::subscribe().

  Payload:
      $event->name        string  The subscriber's name
      $event->email       string  Their email address

  Example uses: send a subscription confirmation email, sync to a mailing
  list provider (Mailchimp, etc.), log the subscription for analytics.

  Note on the shipped SendSubscriptionConfirmation listener:
  The listener that ships with this event has an empty handle() body by design.
  Whether to send an instant confirmation email on sign-up is an
  application-level decision — some apps do; others defer it (e.g. batch-send
  via a cron job after admin review). If your app wants instant confirmation,
  add the sending logic directly inside SendSubscriptionConfirmation::handle().


  ContactFormSubmitted                    src/events/ContactFormSubmitted.php
  ---------------------
  Fired after a visitor successfully submits the contact form via
  FeedbackController::processContact().

  Note: the admin notification email is already sent synchronously by the
  controller. This event is for any additional reactions on top of that —
  the controller handles the admin side, the event handles everything else.

  Payload:
      $event->name        string  The visitor's name
      $event->email       string  Their email address
      $event->phone       string  Their phone number (may be empty)
      $event->message     string  The message body

  Example uses: send a receipt/confirmation email to the visitor, log the
  submission to an analytics or CRM system, trigger a follow-up workflow.



  Built-in listeners shipped with each event
  -------------------------------------------
  Each core event ships with one or more pre-wired listeners in
  src/listeners/. These work out of the box and serve as concrete examples
  to build on:

      Event                    Listener(s)
      ──────────────────────── ─────────────────────────────────────────
      UserRegistered           SendWelcomeEmail
                               LogUserRegistration
      UserLoggedIn             LogUserLogin
      UserLoggedOut            LogUserLogout
      UserSubscribed           SendSubscriptionConfirmation
      ContactFormSubmitted     SendContactConfirmation

  All mappings live in configs/events.php. That file is the single source
  of truth for which listeners respond to which events.


  Adding your own listener to a core event
  -----------------------------------------
  To react to a core event, you do not touch any controller. You only need
  two things:

  Step 1 — Create your listener in src/listeners/

      // src/listeners/SyncNewUserToCrm.php
      namespace Dorguzen\Listeners;

      use Dorguzen\Events\UserRegistered;

      class SyncNewUserToCrm
      {
          public function handle(UserRegistered $event): void
          {
              // push $event->email and $event->username to your CRM API
          }
      }

  Step 2 — Register it in configs/events.php

      \Dorguzen\Events\UserRegistered::class => [
          \Dorguzen\Listeners\SendWelcomeEmail::class,
          \Dorguzen\Listeners\LogUserRegistration::class,
          \Dorguzen\Listeners\SyncNewUserToCrm::class,   // ← add here
      ],

  That's it. The next time a user registers, all three listeners fire in
  the order listed.


  Logging inside listeners — use DGZ_Logger
  -------------------------------------------
  When a listener needs to write to the log, always use DGZ_Logger rather
  than accessing the Logs model directly. DGZ_Logger is the correct tool
  because it:

    - Respects the APP_LOG_DRIVER setting in .env (file, db, or both)
    - Applies the correct log format (APP_LOG_FORMAT)
    - Supports named channels for routing specific events to dedicated logs

  Basic usage (logs to whatever driver .env specifies):

      use Dorguzen\Core\DGZ_Logger;

      DGZ_Logger::info('User registered', [
          'user_id'  => $event->userId,
          'username' => $event->username,
      ]);

  Routing to a dedicated channel (e.g. a separate auth log):

      DGZ_Logger::channel('auth')->info('User logged in', [
          'user_id'  => $event->userId,
          'username' => $event->username,
      ]);

  Available severity levels: debug, info, warning, error.

  Using the Logs model directly (container(Logs::class)->log(...)) bypasses
  all of this and always writes to the database regardless of your .env
  settings — avoid it inside listeners.


  Making a listener asynchronous (ShouldQueue)
  ---------------------------------------------
  By default all listeners run synchronously — they complete before the
  HTTP response is sent. For slow operations like sending emails or calling
  external APIs, implement ShouldQueue to push the listener onto the queue
  instead:

      use Dorguzen\Core\Events\ShouldQueue;

      class SendWelcomeEmail implements ShouldQueue
      {
          public function handle(UserRegistered $event): void
          {
              // this now runs in the background via the queue worker
          }
      }

  The queue worker must be running for queued listeners to execute:

      php dgz queue:work

  See the Queue section of these docs for full details on queue drivers,
  workers, and monitoring.






  SLACK NOTIFICATIONS
  ====================

  Dorguzen has built-in support for sending Slack messages via Incoming
  Webhooks — no Slack SDK or third-party package required. All communication
  is a single HTTPS POST request handled by the framework class DGZ_Slack.



  What are Slack Incoming Webhooks?
  ----------------------------------
  An Incoming Webhook is a URL that Slack generates for your workspace. Any
  application that can make an HTTP POST request can post a message to that
  URL and it will appear instantly in a Slack channel. This is the official,
  simplest way for external systems to send messages to Slack — used by
  everything from GitHub and Jira to custom enterprise tooling.

  You do not need to create a Slack bot, manage OAuth tokens, or install
  anything on the server beyond what Dorguzen already provides.


  1️⃣  Create a Slack Incoming Webhook
  --------------------------------------
  1. Go to https://api.slack.com/apps and sign in.
  2. Click "Create New App" → "From scratch". Give it a name (e.g. "MyApp
     Alerts") and select your workspace.
  3. In the left sidebar click "Incoming Webhooks" and toggle it ON.
  4. Click "Add New Webhook to Workspace", choose the channel you want
     messages to go to, and click Allow.
  5. Copy the Webhook URL — it looks like:
         https://hooks.slack.com/services/T.../B.../xxxxxxxxxxxx


  2️⃣  Configuration
  -------------------
  Add these keys to your .env:

      SLACK_WEBHOOK_URL=https://hooks.slack.com/services/T.../B.../xxx
      SLACK_DEFAULT_CHANNEL=#general    # optional — overrides webhook default
      SLACK_USERNAME=MyApp              # optional — bot display name
      SLACK_ICON_EMOJI=:bell:           # optional — icon shown next to name

  Only SLACK_WEBHOOK_URL is required. The others are optional refinements.

  Channel priority (highest to lowest):
    1. Channel passed directly to DGZ_Slack::send() as second argument
    2. SLACK_DEFAULT_CHANNEL in .env
    3. The channel configured on the webhook itself in the Slack UI


  3️⃣  Sending a message
  -----------------------
  Import and call the static method from anywhere in your application:

      use Dorguzen\Core\DGZ_Slack;

      // Simplest possible call — posts to your configured default channel
      DGZ_Slack::send('New user registered: john_doe');

      // Override the channel for this specific message
      DGZ_Slack::send('Payment failed for order #1234', '#payments');

      // Rich message with an attachment (colour-coded sidebar)
      DGZ_Slack::send('Deploy complete', '#deployments', [
          'attachments' => [[
              'color' => 'good',          // good=green, warning=yellow, danger=red
              'title' => 'Version 2.1.0',
              'text'  => 'Deployed to production at 14:32 UTC.',
          ]]
      ]);

  The third argument $extra is merged directly into the Slack payload, so
  any field the Slack API supports (blocks, thread_ts, mrkdwn, etc.) can
  be passed here.

  DGZ_Slack::send() returns true on success and false on failure.
  Failures are automatically logged via DGZ_Logger so you never lose the
  error silently.

  If SLACK_WEBHOOK_URL is not set, the call returns false immediately and
  logs a warning — it will not throw an exception or crash your application.


  4️⃣  Three ways to trigger a Slack notification
  -------------------------------------------------
  You have three clean options depending on your use case:


  ── Option A: Call DGZ_Slack::send() directly ──────────────────────────
  Use this for one-off notifications where you have the context right there
  in a controller or service method:

      // In a controller or service
      DGZ_Slack::send("New gold member upgrade: {$username}");

  This is synchronous — the Slack API call happens inline before the web
  response is returned. Fine for admin actions; for high-traffic user-facing
  routes, prefer Option B or C.


  ── Option B: Dispatch a queued Job ─────────────────────────────────────
  Use this when you want the Slack call to be non-blocking — the web
  request returns instantly and a background worker sends the message:

      // src/jobs/NotifySlack.php
      class NotifySlack
      {
          public function __construct(
              public string  $message,
              public ?string $channel = null,
          ) {}

          public function handle(): void
          {
              DGZ_Slack::send($this->message, $this->channel);
          }
      }

      // Dispatched from anywhere — fire and forget
      dispatch(new NotifySlack('Order #1234 placed', '#orders'));

  With QUEUE_DRIVER=db or rabbitmq the message is queued instantly and sent
  by a worker in the background. The user never waits for the Slack API.


  ── Option C: A Listener on an existing Event ───────────────────────────
  Use this when a Slack notification is a natural reaction to something that
  already fires an event. No new dispatch call needed anywhere — the event
  already fires and the listener reacts:

      // src/listeners/NotifySlackOnRegistration.php
      class NotifySlackOnRegistration
      {
          public function handle(UserRegistered $event): void
          {
              DGZ_Slack::send(
                  "New user registered: {$event->username} ({$event->email})",
                  '#signups'
              );
          }
      }

      // configs/events.php — just add the listener to the existing array
      UserRegistered::class => [
          SendWelcomeEmail::class,
          LogUserRegistration::class,
          NotifySlackOnRegistration::class,   // ← add this line
      ],

  The controller dispatching the UserRegistered event does not change at all.
  It does not know Slack exists. This is the cleanest, most decoupled approach.

  To make it non-blocking, implement ShouldQueue on the listener:

      class NotifySlackOnRegistration implements ShouldQueue
      {
          public function handle(UserRegistered $event): void
          {
              DGZ_Slack::send("New user: {$event->username}", '#signups');
          }
      }


  5️⃣  Common use cases
  ----------------------
  Slack notifications are most valuable for internal operational awareness
  — things your team needs to know about in real time:

  Use case                          Channel         Example message
  --------------------------------  --------------  --------------------------------
  New user registration             #signups        "New user: john_doe (john@...)"
  Payment received                  #payments       "Order #1234 — $99.99 received"
  Payment failed                    #payments       "⚠ Payment failed: order #1234"
  New contact form submission       #support        "Contact from Jane Smith"
  Error alert (500 / exception)     #alerts         "Fatal error in ProductController"
  Background job failure            #alerts         "Job SendInvoiceJob failed (3/3)"
  Gold/premium membership upgrade   #upgrades       "john_doe upgraded to Gold"
  New shop created                  #shops          "New shop: CamStyle opened"
  Scheduled task ran / failed       #ops            "Backup job completed at 03:00"
  Deployment complete               #deployments    "v2.1.0 deployed to production"

  The pattern is the same for all of them: one listener, one DGZ_Slack::send() call.


  6️⃣  Error handling and resilience
  ------------------------------------
  DGZ_Slack is designed to be a silent helper — it will never crash your
  application if Slack is unreachable:

  - If SLACK_WEBHOOK_URL is missing → logs a warning, returns false.
  - If the Slack API returns a non-200 response → logs the error with full
    context (HTTP code, response body), returns false.
  - If the cURL request times out (5 second limit) → logged, returns false.

  If you want to react to a failure, check the return value:

      if (! DGZ_Slack::send($message, '#alerts')) {
          // Slack is down — fall back to email or just log it
          DGZ_Logger::error('Slack notification failed', ['message' => $message]);
      }



  7️⃣  Advanced payloads — Slack Block Kit
  ------------------------------------------
  Slack supports rich, interactive message layouts via Block Kit. Pass the
  blocks key in the $extra argument to use them:

      DGZ_Slack::send('', '#alerts', [
          'blocks' => [
              [
                  'type' => 'header',
                  'text' => ['type' => 'plain_text', 'text' => 'New Order Received'],
              ],
              [
                  'type'   => 'section',
                  'fields' => [
                      ['type' => 'mrkdwn', 'text' => '*Order:* #1234'],
                      ['type' => 'mrkdwn', 'text' => '*Amount:* $99.99'],
                      ['type' => 'mrkdwn', 'text' => '*Customer:* john_doe'],
                  ],
              ],
          ],
      ]);

  The text parameter becomes the fallback for notifications that cannot render
  blocks (e.g. mobile push previews). Pass an empty string if you are using
  blocks exclusively.

  Use the Slack Block Kit Builder at https://app.slack.com/block-kit-builder
  to design and preview block layouts visually before coding them.








        Social Media Share Buttons (DGZ_Share)
        -----------------------------------------

  DGZ_Share renders a row of platform share-icon buttons for any URL.
  It is a zero-dependency, pure HTML/CSS/JS widget — no SDK, no third-party
  script tag, no cookies. Styles and the copy-to-clipboard script are
  injected into the page once via a PHP static flag, so calling shareButtons()
  multiple times on the same page (e.g. a listing page with many ads) is safe
  and produces only one <style>/<script> block.



        Supported platforms
        --------------------

  | Key        | Share mechanism                                              |
  |------------|--------------------------------------------------------------|
  | facebook   | facebook.com/sharer/sharer.php?u={url}                       |
  | whatsapp   | wa.me/?text={title}+{url}                                    |
  | twitter    | twitter.com/intent/tweet?url={url}&text={title}              |
  | email      | mailto:?subject={title}&body={url}  (opens native mail app)  |
  | copy       | Copies URL to clipboard via navigator.clipboard (JS)         |

  Note on TikTok: TikTok has no web-based share URL. It is a video creation
  platform — there is no "share a link to TikTok" endpoint. Use the 'copy'
  button instead; users tap it, then paste the URL into any TikTok caption,
  bio, or DM. This covers the TikTok use-case without fabricating a fake URL.



        Quick start
        -----------

  In any view, controller, or layout, call the global helper:

        <?= shareButtons($url, $title) ?>

  That's all. The HTML, CSS, and JS are returned as a string — echo it
  wherever you want the buttons to appear on the page.



        The shareButtons() helper
        --------------------------

  Signature:

        shareButtons(string $url, string $title = '', array $options = []): string


  Parameters:

    $url      The canonical URL of the page/item being shared.
    $title    A short description sent with the share (pre-filled tweet text,
              email subject, WhatsApp message prefix, etc.).
    $options  Associative array of optional settings — see below.



        Options
        --------

  | Key         | Type   | Default                           | Description                                 |
  |-------------|--------|-----------------------------------|---------------------------------------------|
  | 'platforms' | array  | ['facebook','whatsapp','twitter', | Which buttons to show, in what order.       |
  |             |        |  'email','copy']                  |                                             |
  | 'label'     | string | 'Share:'                          | Text before the buttons. '' to hide it.     |
  | 'size'      | int    | 38                                | Button diameter in pixels.                  |
  | 'class'     | string | ''                                | Extra CSS class(es) on the wrapper div.     |



        Usage examples
        ---------------

  1. Basic — all platforms, default label:

        <?= shareButtons('https://camerooncom.com/ad/123', 'Samsung TV 55"') ?>


  2. WhatsApp + copy only — good for a mobile-first listing card:

        <?= shareButtons($adUrl, $adTitle, ['platforms' => ['whatsapp', 'copy']]) ?>


  3. Hide the "Share:" label:

        <?= shareButtons($adUrl, $adTitle, ['label' => '']) ?>


  4. Larger buttons with a custom wrapper class for extra spacing:

        <?= shareButtons($adUrl, $adTitle, ['size' => 44, 'class' => 'mt-3']) ?>


  5. Typical use in an ad details view:

        <?php
            $shareUrl   = env('APP_LIVE_URL') . 'ad/' . $ad->id;
            $shareTitle = $ad->title . ' — on Camerooncom';
        ?>
        <?= shareButtons($shareUrl, $shareTitle) ?>



        Using the class directly
        -------------------------

  The helper is a thin wrapper. You can also call the class method directly
  from a controller or service if you need to capture the HTML string first:

        use Dorguzen\Core\DGZ_Share;

        $html = DGZ_Share::buttons($url, $title, $options);



        Where the files live
        ---------------------

  | File                          | Role                                         |
  |-------------------------------|----------------------------------------------|
  | core/DGZ_Share.php            | The class — SVG icons, share URLs, CSS, JS   |
  | bootstrap/helpers.php         | shareButtons() global helper function        |




        Third-Party Service Integrations
        ---------------------------------

  Dorguzen ships with two example module integrations to show you how to connect external services.
  Both follow the same pattern: credentials live exclusively in .env, the module controller reads
  them with env(), and the module can be toggled on or off via the MODULES_*_STATUS flags.


        Twilio SMS
        ----------

  Dorguzen ships with an SMS module that integrates with Twilio to send text messages.
  The module lives at modules/sms/ and its controller at modules/sms/controllers/SmsController.php.

  SETUP
  -----
  1. Install the Twilio SDK:

         composer require twilio/sdk

  2. Sign up or log in at https://www.twilio.com/
     Then go to https://console.twilio.com/ to find your credentials.

  3. Add the following to your .env file:

         TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
         TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
         TWILIO_FROM_NUMBER=+13656542054
         TWILIO_TO_NUMBER=+14378496403

     TWILIO_SID and TWILIO_AUTH_TOKEN are on the main console dashboard.
     Get them from: https://console.twilio.com/us1/account/keys-credentials/api-keys
     Remember to choose the right environment (test or live).

  4. Enable the module in .env:

         MODULES_SMS_STATUS=on

  HOW IT WORKS
  ------------
  The SmsController reads all four values from env() and passes them to the Twilio SDK Client.
  The notify() method (endpoint: /sms/notify) is a working demo that sends a test SMS.
  In a real application, you would replace the hardcoded message body and recipient number
  with dynamic values from your application logic (e.g. user input, order data, etc.).

  GEOGRAPHIC PERMISSIONS
  ----------------------
  For messages to be sent to a given country, you must enable that geographic region in the
  Twilio console under "SMS Geographic Permissions".

  TRIAL ACCOUNTS
  --------------
  On a Twilio trial account you can only send messages to phone numbers you have explicitly
  verified in the "Verified Caller IDs" section of the console. Once you upgrade to a live
  account, this restriction is lifted and you can send to any number within your permitted
  geographic regions.

  FROM NUMBER
  -----------
  TWILIO_FROM_NUMBER must be a Twilio-purchased phone number from your console
  (https://console.twilio.com — Phone Numbers > Manage > Active Numbers).


        Stripe Payments
        ---------------

  Dorguzen ships with a payments module that integrates with Stripe to handle online payments.
  The module lives at modules/payments/ and its controller at modules/payments/controllers/PaymentsController.php.

  SETUP
  -----
  1. Install the Stripe PHP SDK:

         composer require stripe/stripe-php

  2. Sign up or log in at https://stripe.com/
     Go to https://dashboard.stripe.com/apikeys to find your API keys.

  3. Add the following to your .env file:

         STRIPE_SECRET_KEY=sk_test_xxxxxxxxxxxxxxxxxxxxxxxx
         STRIPE_PUBLISHABLE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxxxxxx

     Use the test keys locally. Swap to the live secret key in production.
     API calls must be made with the SECRET key — the publishable key alone is not sufficient.

  4. Enable the module in .env:

         MODULES_PAYMENTS_STATUS=on

  HOW IT WORKS
  ------------
  The PaymentsController reads STRIPE_SECRET_KEY via env() and passes it to \Stripe\Stripe::setApiKey()
  in the constructor, so all subsequent Stripe API calls in that request are authenticated.

  The module ships with two payment methods as examples:

    pay()   — Stripe Checkout Session method (recommended for most use cases).
              Good for single or multiple products. Redirects the customer to Stripe's hosted
              checkout page and returns them to your success or cancel URL when done.
              See: https://docs.stripe.com/payments/checkout/how-checkout-works

    pay2()  — Direct charge method using a Stripe token.
              Good for a single fixed amount. Expects a stripe_token in the POST body
              (generated client-side by Stripe.js). Note: this method requires a valid SSL
              certificate — it will not work on localhost unless you have SSL configured,
              because Stripe.js will not generate a valid token over plain HTTP.

  CONFIRMING PAYMENTS
  -------------------
  After a payment, log in to the Stripe dashboard and go to:
    Dashboard > Payments > switch to the relevant mode (Test / Live) to see all transactions.

  SUCCESS AND CANCEL URLS
  -----------------------
  The pay() method builds its redirect URLs dynamically from the application's base URL
  (via $this->config->getHomePage()), so they automatically point to the right host in
  both local and production environments. You do not need to configure these separately.

  PRICE IDS
  ---------
  The example in pay() references a Price ID (e.g. price_1OpXKWFRQteXl4yngb9PyxJj).
  You can find or create Price IDs in the Stripe dashboard:
    Dashboard > Product catalog > select a product > copy the API ID from the price row.






——————————————————————
	LOCALISATION
——————————————————————

-Talks about the tools that your 
  programming language/framework offers 
  you to dynamically translate your app for 
  deployment in different geographic 
  locations.




——————————————————————
	SYSTEMS AND PERFORMANCE 
——————————————————————

This helps you understand how to set up your computer to code in your chosen programming language. It looks at the tooling needed, starting with the operating system, for example; Windows, macOS, Linux etc.
  This also involves learning about servers, scaling platforms to handle workloads, and caching etc.




——————————————————————
	COLLABORATION
——————————————————————

-This discusses the tools made available by 
  your programming language of choice to 
  collaborate with other developers. 
  Examples are DB migrations which offers 
  versions in terms of the time frames, and 
  the ability to roll back database 
  migrations.
-The other option is to learn git.