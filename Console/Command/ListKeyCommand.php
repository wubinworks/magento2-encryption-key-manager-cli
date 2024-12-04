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
use Wubinworks\EncryptionKeyManagerCli\Model\Encryption\KeyManager;

class ListKeyCommand extends Command
{
    /**
     * @var KeyManager
     */
    protected $keyManager;

    /**
     * Constructor
     *
     * @param KeyManager $keyManager
     * @param ?string $name
     */
    public function __construct(
        KeyManager $keyManager,
        ?string $name = null
    ) {
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
        $this->setName('ww:encryption-key-manager:list')
            ->setAliases(['ww:ekm:list'])
            ->setDescription('List all encryption key. The last one is the newest key.')
            ->setHelp(
                'Display encryption keys in your current Magento installation.'
            )
            ->addOption(
                'newest',
                null,
                InputOption::VALUE_NONE,
                'Display newest encryption key only.'
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
        $keys = $this->keyManager->getKeys();
        if (!count($keys)) {
            $output->writeln('<fg=red>' . 'No encryption key.' . '</>');
            return Command::FAILURE;
        }

        if ($input->getOption('newest')) {
            $keys = [end($keys)];
        } else {
            $output->writeln('<fg=white>' . sprintf('Encryption key count: %d', count($keys)) . '</>');
        }

        foreach ($keys as $key) {
            $output->writeln('<fg=white>' . $key . '</>');
        }

        return Command::SUCCESS;
    }
}
