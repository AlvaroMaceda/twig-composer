# Twig Composer

A callback function library for Twig: you will be able to execute code when templates are rendered.

Useful for calling code like controllers, etc. before the template is rendered. This can be
done in some frameworks as Symfony or Laravel, but this library works directly with Twig.

For example, if you have a ```menu.twig``` template which can be included in a layout, you can do:

```
    TwigComposer::getNotifier()->on('menu.twig', [$menuService,generateDinamicMenu]);
```

And then use the menu generated in ```menu.twig``` template. You won't need to include a call to
the service in each controller which uses the layout.

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

To receive callbacks you must register them for each template you want to be notified:

```
TwigComposer::getNotifier()->on('Template_I_Want_To_Watch', $callback);
```

```$callback``` will be called each time ```'Template_I_Want_To_Watch'``` is rendered.

You can also inherit your own base class from TwigComposer if you need to use your own base class for Twig (do not
forget to call parent methods when corresponding)

### Installation

If you do not use composer, you must install and require the following dependencies:

- [christopherobin/EventEmitter][christopherobin/EventEmitter]
- [Twig][twig]

You must also include ```Relayer.php``` and ```TwigComposer.php``` in your project to use this library.

## Running the tests

This library uses PHPUnit for test. To run the test, execute this command:
```
/usr/bin/php ./vendor/phpunit/phpunit/phpunit ./tests
```

If you want to generate code coverage information, just add the option ```--coverage-html DIRECTORY```

## Contributing

Feel free to contribute to this code. Please send an email to the authors and / or
a pull request to the project's repository: https://github.com/AlvaroMaceda/twig-composer


## Authors

* **[Alvaro Maceda][AlvaroMaceda]** - *Initial work*

## License

This project is Public Domain.

## Acknowledgments

* Thanks to [Christophe Robin][christopherobin] for his port of [event emmiter][christopherobin/EventEmitter] to PHP
* Inspired by View::composer feature of Laravel


[AlvaroMaceda]: <http://alvaromaceda.es>
[christopherobin/EventEmitter]: <http://daringfireball.net/projects/markdown/>
[christopherobin]: <https://github.com/christopherobin>
[twig]: <http://twig.sensiolabs.org/>