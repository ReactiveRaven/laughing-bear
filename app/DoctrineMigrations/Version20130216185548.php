<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130216185548 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE Action (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, action VARCHAR(255) NOT NULL, json LONGTEXT NOT NULL, active TINYINT(1) NOT NULL, complete TINYINT(1) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_406089A4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE Action ADD CONSTRAINT FK_406089A4A76ED395 FOREIGN KEY (user_id) REFERENCES GithubUser (id)");
        $this->addSql("ALTER TABLE GithubAccessToken DROP FOREIGN KEY FK_D806DE92A76ED395");
        $this->addSql("DROP INDEX UNIQ_D806DE92A76ED395 ON GithubAccessToken");
        $this->addSql("ALTER TABLE GithubAccessToken DROP user_id");
        $this->addSql("ALTER TABLE GithubRateLimit DROP FOREIGN KEY FK_D258B28C4C5BE87");
        $this->addSql("DROP INDEX UNIQ_D258B28C4C5BE87 ON GithubRateLimit");
        $this->addSql("ALTER TABLE GithubRateLimit DROP accessToken_id");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE Action");
        $this->addSql("ALTER TABLE GithubAccessToken ADD user_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE GithubAccessToken ADD CONSTRAINT FK_D806DE92A76ED395 FOREIGN KEY (user_id) REFERENCES GithubUser (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_D806DE92A76ED395 ON GithubAccessToken (user_id)");
        $this->addSql("ALTER TABLE GithubRateLimit ADD accessToken_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE GithubRateLimit ADD CONSTRAINT FK_D258B28C4C5BE87 FOREIGN KEY (accessToken_id) REFERENCES GithubAccessToken (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_D258B28C4C5BE87 ON GithubRateLimit (accessToken_id)");
    }
}
