![ha framework](img/ha-logo.png "ha framework")

# Console application in *ha* framework

Console application is an application runned from the command line. This application can be runned from your project directory ([ha project skeleton](https://github.com/itrnka/ha-project-skeleton/blob/master/README.md) installation is required). Our application is based on *Symfony Console Component* and ha framework provides simple access to this functionality. You can write custom commands with some custom functionality.

For more informations about *Symfony Console Component* please see [official documentation](https://symfony.com/doc/current/components/console.html).

Command in our case is class instance, which functionality is very similary to route and also controller. So console application works as router, but we have shell arguments instead of HTTP request and URL. So we can use our commands likewise as controllers.

## Running *ha* application on Linux systems

Executing *ha* application from command line from your project directory:

`./bin/ha`

This runs our application without specific command. We have predefined command `examples:hello-world` in our project after project installation and we can execute this command by calling:

`./bin/ha examples:hello-world`


## Running *ha* application on Windows systems

Executing *ha* command line application from from your project directory:

Open `bin/ha.bat` by double clicking to this file. You can see some informations about your application. Please enter this and press enter:

`php ha`

This runs our application without specific command. We have predefined command `examples:hello-world` in our project after project installation and we can execute this command by calling:

`php ha examples:hello-world`

## How to add new command to our application

Commands list is defined in configuration file for console application. Default location for this file is `{projectRoot}/php/conf/console.conf.php`. We can change environment name for console application in file `{projectRoot}/bin/ha.ini` and then location of configuration file will be `{projectRoot}/php/conf/{newName}.conf.php`.

Please see [application configuration](app-configuration.md) for more details. 


## Command class example

```php
<?php
declare(strict_types=1);

namespace Examples\ConsoleAccess;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorldCommand extends Command
{

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        // the name of the command (the part after "bin/console.php")
        $this->setName('examples:hello-world');

        // the short description shown while running "php bin/console list"
        $this->setDescription('Hello world example.');

        // the full command description shown when running the command with the "--help" option
        $this->setHelp('Hello world help...');

        // hide command or show command
        $this->setHidden(false);

        // add arguments
        #$this->addArgument('name', InputArgument::REQUIRED, 'Please enter your name', null);

        // add options
        #$this->addOption('xvalue', null, InputOption::VALUE_REQUIRED, 'xvalue description', null);
    }

    /**
     * Executes the current command.
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     * @throws \LogicException When this abstract method is not implemented
     * @see setCode()
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello world!');
        return null;
    }


}


```