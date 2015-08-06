<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new AppBundle\AppBundle(),

	        new FOS\UserBundle\FOSUserBundle(),
	        new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
			new JMS\SerializerBundle\JMSSerializerBundle(),

	        new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
	        new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
	        new Sonata\MediaBundle\SonataMediaBundle(),
	        new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
	        new Sonata\IntlBundle\SonataIntlBundle(),
	        new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
	        new Sonata\CoreBundle\SonataCoreBundle(),
	        new Sonata\BlockBundle\SonataBlockBundle(),
	        new Knp\Bundle\MenuBundle\KnpMenuBundle(),
	        new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
	        new Sonata\AdminBundle\SonataAdminBundle(),
	        new Sonata\ClassificationBundle\SonataClassificationBundle(),
	        new Application\Sonata\ClassificationBundle\ApplicationSonataClassificationBundle(),

	        // news
	        new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
	        new Sonata\NewsBundle\SonataNewsBundle(),
	        new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
	        new Sonata\FormatterBundle\SonataFormatterBundle(),
	        new \Application\Sonata\NewsBundle\ApplicationSonataNewsBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    private $configDir = '/var/lib/samsonit';
    private $currentDir = __DIR__;
    private $paramsFile = "/config/parameters.yml";

    /**
     * Verify that /var/lib/samsonit exists
     * This is where the parameters.yml configs are stored.
     * @throws \LogicException when this does not exist.
     * @return bool
     */
    private function doesConfigDirExist() {
        if(!is_dir($this->configDir)) {
            throw new LogicException("{$this->configDir} not found! Create it and place your parameters.yml there! see http://wiki.samson-it.local/knowledge.base:development:symfony2:parameters.yml!");
        }
        return true;
    }

    /**
     * Some Posix calls to determine the user that is executing the script.
     * Note: this is *NOT* the file owner or group, it's the account that executes the script!
     * @return string
     */
    private function getProcessUser()
    {
        $process = posix_getpwuid(posix_geteuid()); // if not, the project config is probably stored under it's NGINX user. grab that.
        $processUser = $process['name'];
        return $processUser;
    }

    /**
     * Grab the base folder for the current project
     * @return string
     */
    private function determineAppNameFromBasePath() {
        return basename(realpath("{$this->currentDir}/../")); // grab the folder that the project lives in.
    }


    private function throwExceptionWithExplanation($explanation) {
        throw new \LogicException("{$explanation}

    To resolve this:
    - make sure /var/lib/samsonit/ exists
    - create the parameters.yml file in a folder named {$this->determineAppNameFromBasePath()} or {$this->getProcessUser()} under {$this->configDir}
    - run app/console cache:clear to auto-create the symlink for you.

    For more info, check the wiki: http://wiki.samson-it.nl/knowledge.base:development:symfony2:parameters.yml");
    }


    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if(!$this->doesConfigDirExist()) {
            $this->throwExceptionWithExplanation("{$this->configDir} does not exist.");
        }

        // params file does not exist, check if we can auto resolve it and create the symlink for the user.
        if (!file_exists($this->currentDir.$this->paramsFile)) {
            $appContext = $this->determineAppNameFromBasePath(); // determine the folder under /var/lib/samsonit where the folder has to be loaded.
            $processUser = $this->getProcessUser();

            if(file_exists("{$this->configDir}/{$appContext}/parameters.yml")) {
                symlink("{$this->configDir}/{$appContext}/parameters.yml", $this->currentDir.$this->paramsFile);
            } elseif(file_exists("{$this->configDir}/{$processUser}/parameters.yml")) {
                symlink("{$this->configDir}/{$processUser}/parameters.yml", $this->currentDir.$this->paramsFile);
            } else {
                $this->throwExceptionWithExplanation("{$this->configDir}/{$appContext}/parameters.yml or {$this->configDir}/{$processUser}/parameters.yml not found!");
            }
        }

        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
    
}
