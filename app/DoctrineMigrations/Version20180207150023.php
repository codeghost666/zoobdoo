<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180207150023 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stripe_subscription DROP INDEX IDX_6F290B43708DC647, ADD UNIQUE INDEX UNIQ_6F290B43708DC647 (stripe_customer_id)');
        $this->addSql('ALTER TABLE stripe_subscription DROP quantity');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stripe_subscription DROP INDEX UNIQ_6F290B43708DC647, ADD INDEX IDX_6F290B43708DC647 (stripe_customer_id)');
        $this->addSql('ALTER TABLE stripe_subscription ADD quantity INT NOT NULL');
    }
}
