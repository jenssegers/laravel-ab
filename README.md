Laravel 4 A/B Testing
=====================

A server-side A/B testing tool for Laravel. It may not have a lot of features or has the prettiest code, but it's a great free server-side A/B testing solution.

This tool allows you to experiment with different variations of your website and tracks what the difference in engagement or reached goals is between them. Whenever you ask the A/B testing class for the current experiment, it will select the next experiment that has the least visits so that every experiment is tested equally. When there is an active experiment going on, it will start tracking engagement (click a different link, or submitting a form) and check if certain defined goals are reached. These goals are generally urls or routes, but can also be triggered manually.

Installation
------------

Add the package to your `composer.json` and run `composer update`.

    {
        "require": {
            "jenssegers/ab": "*"
        }
    }

Add the service provider in `app/config/app.php`:

    'Jenssegers\AB\TesterServiceProvider',

Register the AB alias:

    'AB'           => 'Jenssegers\AB\Facades\AB',

Configuration
-------------

Publish the included configuration file:

    php artisan config:publish jenssegers/ab

Next, edit the `config/packages/jenssegers/ab/config.php` file. The following configuration options are available:

### Database Connection

This is your Laravel database connection that is used to store the A/B testing data. By default, it will use your default connection, but here you can change it to store data in a different database.

    'connection' => 'mysql',

### Experiments

These are your A/B experiments. These are unique identifiers that you can use in your code or views to decide which version you should be showing.

    'experiments' => [
        'big-logo',
        'small-buttons',
        'short-form'
    ],

### Goals

Without goals, each experiment will track the number of visitors that saw the experiment and detect engagement. Additionally, you can define certain goals that you want to reach with your experiments. If, for example, your main goal is for your visitors to buy your product or contact you for more information and you have specific routes set up for both of these pages, your goals could look like this:

    'goals' => [
        'pricing/order',
        'contact'
    ]

Your goals can be relative urls, named routes or can be triggered manually.

Preparing the A/B test database
-------------------------------

Once you have selected your database connection, use the included install command to prepare the required tables:

    php artisan ab:install

Usage
-----

After you have defined your experiments and goals, you can start designing your A/B tests. All your visitors will be given the next experiment that has the least visits. You can request the current experiment identifier with the `AB::experiment()` method. For example, if you have defined the following experiments ['a', 'b', 'c'], your view could look like this:

    @if (AB::experiment('a'))
        <div class="logo-big"></div>

    @elseif (AB::experiment('b'))
        <h1>Brand name</h1>

    @elseif (AB::experiment('c'))
        <div class="logo-greyscale"></div>

    @endif

Once the visitor is assigned to an experiment, his next clicks are automatically tracked to see if he is engaging with your website or completing certain goals. Your goals can be relative urls or named routes, when a visitor clicks that link during an experiment, it will be marked as completed for that visitor.

### Adding new experiments

If you want to add new experiments, it may be best to clear the existing A/B testing data with this command:

    php artisan ab:flush

If you don't flush your existing experimental data, all new visitors will see the new experiment first until it catches up with the pageviews of the old experiments.

Reports
-------

A/B testing reports are available through an artisan command:

    php artisan ab:report

This will generate a simple output containing the results for each experiment and their goals.

    +------------+----------+----------------+---------------+---------------+---------------+
    | Experiment | Visitors |     Engagement |           Buy |       Contact |       Pricing |
    +------------+----------+----------------+---------------+---------------+---------------+
    |          a |  173,074 | 6.0 % (10,363) | 1.3 % (2,249) | 4.8 % (8,307) | 5.3 % (9,172) |
    |          b |  173,073 |  5.1 % (8,826) | 1.1 % (1,903) | 3.5 % (6,057) | 3.9 % (6,749) |
    |          c |  173,073 |  5.0 % (8,653) | 1.0 % (1,730) | 1.3 % (5,538) | 3.2 % (5,538) |
    +------------+----------+----------------+---------------+---------------+---------------+

Advanced
--------

### AB::complete($goal)

Used to manually trigger goals. Useful when you want to track goals that are not linked to urls or routes.

### AB::interact()

Used to manually trigger an interaction which results in engagement.
