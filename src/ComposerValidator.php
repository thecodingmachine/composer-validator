<?php
namespace Mouf\Composer\Validator;

use Mouf\Validator\MoufStaticValidatorInterface;
use Mouf\Validator\MoufValidatorResult;

class ComposerValidator implements MoufStaticValidatorInterface {

    /**
     * Runs the validation of the class.
     * Returns a MoufValidatorResult explaining the result.
     *
     * @return MoufValidatorResult
     */
    static function validateClass(){
        $oldCwd = getcwd();
        chdir(__DIR__.'/../../../../');

        $io = new \Composer\IO\BufferIO();
        $composer = \Composer\Factory::create($io);
        $installer = \Composer\Installer::create($io, $composer);
        $installer->setDryRun(true);
        $installer->run();
        $output = $io->getOutput();
        $errors = array();
        $warnings = array();
        $message = "";
        preg_match_all('/(?<=<error>)(.*?)(?=<\/error>)/', $output, $errors);
        preg_match_all('/(?<=<warning>)(.*?)(?=<\/warning>)/', $output, $warnings);
        if (isset($errors[0]) && $errors[0]) {
            foreach($errors[0] as $error){
                $message .= $error.'</br>';
            }
            return new MoufValidatorResult(MoufValidatorResult::ERROR, "<strong>Composer</strong>: $message");
        } elseif (isset($warnings[0]) && $warnings[0]) {
            foreach($warnings[0] as $warning){
                $message .= $warning.'</br>';
            }
            return new MoufValidatorResult(MoufValidatorResult::WARN, "<strong>Composer</strong>: $message");
        } else{
            return new MoufValidatorResult(MoufValidatorResult::SUCCESS, "<strong>Composer</strong>: All good");
        }

        chdir($oldCwd);
    }
}