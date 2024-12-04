<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\EncryptionKeyManagerCli\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\App\CacheInterface;
use Wubinworks\EncryptionKeyManagerCli\Model\Encryption\KeyManager;

class RotateKeyCommand extends Command
{
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Configuration writer
     *
     * @var Writer
     */
    protected $writer;

    /**
     * @var KeyManager
     */
    protected $keyManager;

    /**
     * Constructor
     *
     * @param CacheInterface $cache
     * @param Writer $writer
     * @param KeyManager $keyManager
     * @param ?string $name
     */
    public function __construct(
        CacheInterface $cache,
        Writer $writer,
        KeyManager $keyManager,
        ?string $name = null
    ) {
        $this->cache = $cache;
        $this->writer = $writer;
        $this->keyManager = $keyManager;
        parent::__construct($name);
    }

    /**
     * Initialization of the command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('ww:encryption-key-manager:rotate')
            ->setAliases(['ww:ekm:rotate'])
            ->setDescription('Rotate encryption key.')
            ->setHelp('For development/deployment automation use.')
            ->addOption(
                'key',
                'k',
                InputOption::VALUE_REQUIRED,
                'If not provided, a random key will be generated.',
                null
            );

        parent::configure();
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = $input->getOption('key');
        if ($key === null) {
            $key = $this->keyManager->generate();
        } elseif (!$this->keyManager->validate($key)) {
            $output->writeln('<fg=red>' . 'Invalid key.' . '</>');
            return Command::FAILURE;
        }

        if (!$this->writer->checkIfWritable()) {
            $output->writeln('<fg=red>' . 'Deployment configuration file is not writable.' . '</>');
            $output->writeln(
                '<fg=yellow>' . 'Check file permission of `app/etc/env.php` and `app/etc/config.php`.' . '</>'
            );
            return Command::FAILURE;
        }

        $this->rotateEncryptionKey($key);
        $this->cache->clean();
        $output->writeln('<fg=green>' . 'Encryption key has been rotated successfully.' . '</>');
        $output->writeln(
            '<fg=yellow>' . 'Encryption keys are stored in `app/etc/env.php`. Caution: do not delete old keys!' . '</>'
        );
        return Command::SUCCESS;
    }

    /**
     * Rotate encryption key
     *
     * @param string $key
     * @return void
     */
    protected function rotateEncryptionKey(string $key): void
    {
        $this->writer->saveConfig([
            \Magento\Framework\Config\File\ConfigFilePool::APP_ENV => [
                'crypt' => [
                    'key' => trim($this->keyManager->getKeys(true) . "\n" . $key, "\n")
                ]
            ]
        ]);
    }
}
