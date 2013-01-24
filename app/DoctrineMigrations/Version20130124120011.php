<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130124120011 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE GithubRepository (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, source_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, github_id INT NOT NULL, pushed_at DATETIME NOT NULL, forks INT NOT NULL, has_issues TINYINT(1) NOT NULL, full_name VARCHAR(255) NOT NULL, forks_count INT NOT NULL, has_downloads TINYINT(1) NOT NULL, svn_url VARCHAR(255) NOT NULL, mirror_url VARCHAR(500) DEFAULT NULL, homepage VARCHAR(500) DEFAULT NULL, language VARCHAR(255) NOT NULL, git_url VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, has_wiki TINYINT(1) NOT NULL, size INT NOT NULL, fork TINYINT(1) NOT NULL, description VARCHAR(500) DEFAULT NULL, clone_url VARCHAR(255) NOT NULL, html_url VARCHAR(255) NOT NULL, watchers INT NOT NULL, watchers_count INT NOT NULL, `name` VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, ssh_url VARCHAR(255) NOT NULL, private TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, open_issues INT NOT NULL, open_issues_count INT DEFAULT NULL, INDEX IDX_E83BC74DA76ED395 (user_id), INDEX IDX_E83BC74D727ACA70 (parent_id), INDEX IDX_E83BC74D953C1C61 (source_id), INDEX IDX_E83BC74D32C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE GithubUser (id INT AUTO_INCREMENT NOT NULL, github_id INT NOT NULL, login VARCHAR(255) NOT NULL, avatar_url VARCHAR(255) NOT NULL, gravatar_id VARCHAR(32) DEFAULT NULL, url VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, blog VARCHAR(255) DEFAULT NULL, html_url VARCHAR(2000) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, hireable TINYINT(1) DEFAULT NULL, bio LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, public_repos INT NOT NULL, followers INT NOT NULL, following INT NOT NULL, public_gists INT NOT NULL, type VARCHAR(255) NOT NULL, accessToken_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_4B97C08C4C5BE87 (accessToken_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE GithubRepositoryPermissions (id INT AUTO_INCREMENT NOT NULL, repository_id INT DEFAULT NULL, push TINYINT(1) NOT NULL, pull TINYINT(1) NOT NULL, admin TINYINT(1) NOT NULL, `user` VARCHAR(255) NOT NULL, INDEX IDX_D09F625350C9D4F7 (repository_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE GithubRateLimit (id INT AUTO_INCREMENT NOT NULL, `limit` INT NOT NULL, remaining INT NOT NULL, refreshTime DATETIME NOT NULL, refreshInterval INT NOT NULL, accessToken_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_D258B28C4C5BE87 (accessToken_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE GithubOrganization (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, following INT NOT NULL, html_url VARCHAR(2000) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, blog VARCHAR(2000) DEFAULT NULL, public_gists INT NOT NULL, name VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL, url VARCHAR(2000) NOT NULL, login VARCHAR(255) NOT NULL, followers INT NOT NULL, avatar_url VARCHAR(2000) NOT NULL, github_id INT NOT NULL, public_repos INT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE GithubAccessToken (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, rateLimit_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_D806DE92F6A18D30 (rateLimit_id), UNIQUE INDEX UNIQ_D806DE92A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE GithubRepository ADD CONSTRAINT FK_E83BC74DA76ED395 FOREIGN KEY (user_id) REFERENCES GithubUser (id)");
        $this->addSql("ALTER TABLE GithubRepository ADD CONSTRAINT FK_E83BC74D727ACA70 FOREIGN KEY (parent_id) REFERENCES GithubRepository (id)");
        $this->addSql("ALTER TABLE GithubRepository ADD CONSTRAINT FK_E83BC74D953C1C61 FOREIGN KEY (source_id) REFERENCES GithubRepository (id)");
        $this->addSql("ALTER TABLE GithubRepository ADD CONSTRAINT FK_E83BC74D32C8A3DE FOREIGN KEY (organization_id) REFERENCES GithubOrganization (id)");
        $this->addSql("ALTER TABLE GithubUser ADD CONSTRAINT FK_4B97C08C4C5BE87 FOREIGN KEY (accessToken_id) REFERENCES GithubAccessToken (id)");
        $this->addSql("ALTER TABLE GithubRepositoryPermissions ADD CONSTRAINT FK_D09F625350C9D4F7 FOREIGN KEY (repository_id) REFERENCES GithubRepository (id)");
        $this->addSql("ALTER TABLE GithubRateLimit ADD CONSTRAINT FK_D258B28C4C5BE87 FOREIGN KEY (accessToken_id) REFERENCES GithubAccessToken (id)");
        $this->addSql("ALTER TABLE GithubAccessToken ADD CONSTRAINT FK_D806DE92F6A18D30 FOREIGN KEY (rateLimit_id) REFERENCES GithubRateLimit (id)");
        $this->addSql("ALTER TABLE GithubAccessToken ADD CONSTRAINT FK_D806DE92A76ED395 FOREIGN KEY (user_id) REFERENCES GithubUser (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE GithubRepository DROP FOREIGN KEY FK_E83BC74D727ACA70");
        $this->addSql("ALTER TABLE GithubRepository DROP FOREIGN KEY FK_E83BC74D953C1C61");
        $this->addSql("ALTER TABLE GithubRepositoryPermissions DROP FOREIGN KEY FK_D09F625350C9D4F7");
        $this->addSql("ALTER TABLE GithubRepository DROP FOREIGN KEY FK_E83BC74DA76ED395");
        $this->addSql("ALTER TABLE GithubAccessToken DROP FOREIGN KEY FK_D806DE92A76ED395");
        $this->addSql("ALTER TABLE GithubAccessToken DROP FOREIGN KEY FK_D806DE92F6A18D30");
        $this->addSql("ALTER TABLE GithubRepository DROP FOREIGN KEY FK_E83BC74D32C8A3DE");
        $this->addSql("ALTER TABLE GithubUser DROP FOREIGN KEY FK_4B97C08C4C5BE87");
        $this->addSql("ALTER TABLE GithubRateLimit DROP FOREIGN KEY FK_D258B28C4C5BE87");
        $this->addSql("DROP TABLE GithubRepository");
        $this->addSql("DROP TABLE GithubUser");
        $this->addSql("DROP TABLE GithubRepositoryPermissions");
        $this->addSql("DROP TABLE GithubRateLimit");
        $this->addSql("DROP TABLE GithubOrganization");
        $this->addSql("DROP TABLE GithubAccessToken");
    }
}
