<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130218140239 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE githubuser_githuborganization");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE githubuser_githuborganization (githubuser_id INT NOT NULL, githuborganization_id INT NOT NULL, INDEX IDX_AC02452C573C845F (githubuser_id), INDEX IDX_AC02452C36AED6B9 (githuborganization_id), PRIMARY KEY(githubuser_id, githuborganization_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE githubuser_githuborganization ADD CONSTRAINT FK_AC02452C36AED6B9 FOREIGN KEY (githuborganization_id) REFERENCES GithubOrganization (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE githubuser_githuborganization ADD CONSTRAINT FK_AC02452C573C845F FOREIGN KEY (githubuser_id) REFERENCES GithubUser (id) ON DELETE CASCADE");
    }
}
