<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151012170149 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'CREATE TABLE service_requests (
            id INT AUTO_INCREMENT NOT NULL,
            from_user_id INT DEFAULT NULL,
            to_user_id INT DEFAULT NULL,
            type_id INT NOT NULL,
            date DATETIME NOT NULL,
            text LONGTEXT NOT NULL,
            created_date DATETIME NOT NULL,
            INDEX IDX_82F38D6C2130303A (from_user_id),
            INDEX IDX_82F38D6C29F6EE60 (to_user_id),
            PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE service_requests ADD CONSTRAINT FK_82F38D6C2130303A FOREIGN KEY (from_user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_requests ADD CONSTRAINT FK_82F38D6C29F6EE60 FOREIGN KEY (to_user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE service_requests');
    }
}
