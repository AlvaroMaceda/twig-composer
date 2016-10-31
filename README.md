# Twig Composer
 
A callback function library for Twig: you can execute code when templates are rendered.

## Getting Started

If you are using composer tool, you can start using TwigComposer requiring the library:

```
require maceda/twig-composer
```

If not, please follow installation instructions.

To be notified of template renders, you need to indicate TwigComposer as the base class
for templates when creating Twig_Environment:

```
$this->twig = new \Twig_Environment(
$loader,
array(
     ...
     'base_template_class' => 'TwigComposer\TwigComposer',
     ...
));
```

You can also inherit your own base class from TwigComposer if you need to use your own base class for Twig (do not
forget to call parent methods when corresponding)

To receive callbacks you must register them for each template you want to be notified:

```
TwigComposer::getNotifier()->on('Template_I_Want_To_Watch', $callback);
```

$callback will be called each time 'Template_I_Want_To_Watch' is rendered.

### Prerequisites

TwigComposer is a library to be notified of Twig template renders, so it requires Twig to
be installed and running.

### Installation

TO-DO

Como instalar eventemitter

, you must include ```Relayer.php``` and ```TwigComposer.php``` in your project. TwigComposer requires
Twig, so it must be installed / included before using TwigComposer. To install Twig please follow
the instructions provided in that library.


## Running the tests

TODO: Explain how to run the automated tests for this system

## Contributing

Feel free to contribute to this code. Please send an email to the authors and / or
a pull request to the project's repository: https://github.com/AlvaroMaceda/twig-composer

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags).

## Authors

* **Alvaro Maceda** - *Initial work* - [AlvaroMaceda](http://alvaromaceda.es)

## License

This project is Public Domain.

## Acknowledgments

* EMMITER
* Inspired by View::composer feature of Laravel

