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

class GenerateKeyCommand extends Command
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
        $this->setName('ww:encryption-key-manager:genkey')
            ->setAliases(['ww:ekm:genkey'])
            ->setDescription('Generate new encryption key.')
            ->setHelp(
                'For development/deployment automation use.' . "\n"
                . 'If `--format|-f` is not provided, use current Magento\'s compactable encryption key type' . "\n"
                . '--format|-f base64 for >= 2.4.7' . "\n"
                . '--format|-f hex for < 2.4.7'
            )
            ->addOption(
                'format',
                'f',
                InputOption::VALUE_REQUIRED,
                'Encryption key type. Possible value: `base64`, `hex`.',
                ''
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
        $output->writeln('<fg=green>' . $this->keyManager->generate($input->getOption('format')) . '</>');
        return Command::SUCCESS;
    }
}
