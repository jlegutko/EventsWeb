<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190903213045 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE interests (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_C8B405EA71F7E88B (event_id), INDEX IDX_C8B405EAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE members (id INT AUTO_INCREMENT NOT NULL, member_id INT UNSIGNED NOT NULL, community_id INT NOT NULL, INDEX IDX_45A0D2FF7597D3FE (member_id), INDEX IDX_45A0D2FFFDA7B0BF (community_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT UNSIGNED AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, email VARCHAR(128) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', first_name VARCHAR(255) NOT NULL, UNIQUE INDEX email_idx (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts (id INT AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, community_id INT NOT NULL, content VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_885DBAFAA76ED395 (user_id), INDEX IDX_885DBAFAFDA7B0BF (community_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grades (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, user_id INT UNSIGNED NOT NULL, grade DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3AE3611071F7E88B (event_id), INDEX IDX_3AE36110A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, owner_id INT UNSIGNED NOT NULL, event_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, content VARCHAR(255) NOT NULL, INDEX IDX_5F9E962A7E3C61F9 (owner_id), INDEX IDX_5F9E962A71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, user_id INT UNSIGNED NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, price DOUBLE PRECISION NOT NULL, place VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, end_date DATETIME NOT NULL, start_date DATETIME NOT NULL, size VARCHAR(255) NOT NULL, INDEX IDX_5387574A12469DE2 (category_id), INDEX IDX_5387574AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profilePhotos (id INT AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, file VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_723696A1A76ED395 (user_id), UNIQUE INDEX UQ_profilePhotos_1 (file), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photos (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, file VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_876E0D971F7E88B (event_id), UNIQUE INDEX UQ_photos_1 (file), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groups (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, author_id INT UNSIGNED NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F06D397071F7E88B (event_id), UNIQUE INDEX UNIQ_F06D3970F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interests ADD CONSTRAINT FK_C8B405EA71F7E88B FOREIGN KEY (event_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE interests ADD CONSTRAINT FK_C8B405EAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE members ADD CONSTRAINT FK_45A0D2FF7597D3FE FOREIGN KEY (member_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE members ADD CONSTRAINT FK_45A0D2FFFDA7B0BF FOREIGN KEY (community_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAFDA7B0BF FOREIGN KEY (community_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE3611071F7E88B FOREIGN KEY (event_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE36110A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A71F7E88B FOREIGN KEY (event_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE profilePhotos ADD CONSTRAINT FK_723696A1A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D971F7E88B FOREIGN KEY (event_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE groups ADD CONSTRAINT FK_F06D397071F7E88B FOREIGN KEY (event_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE groups ADD CONSTRAINT FK_F06D3970F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE interests DROP FOREIGN KEY FK_C8B405EAA76ED395');
        $this->addSql('ALTER TABLE members DROP FOREIGN KEY FK_45A0D2FF7597D3FE');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFAA76ED395');
        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE36110A76ED395');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A7E3C61F9');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AA76ED395');
        $this->addSql('ALTER TABLE profilePhotos DROP FOREIGN KEY FK_723696A1A76ED395');
        $this->addSql('ALTER TABLE groups DROP FOREIGN KEY FK_F06D3970F675F31B');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A12469DE2');
        $this->addSql('ALTER TABLE interests DROP FOREIGN KEY FK_C8B405EA71F7E88B');
        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE3611071F7E88B');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A71F7E88B');
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY FK_876E0D971F7E88B');
        $this->addSql('ALTER TABLE groups DROP FOREIGN KEY FK_F06D397071F7E88B');
        $this->addSql('ALTER TABLE members DROP FOREIGN KEY FK_45A0D2FFFDA7B0BF');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFAFDA7B0BF');
        $this->addSql('DROP TABLE interests');
        $this->addSql('DROP TABLE members');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE grades');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE profilePhotos');
        $this->addSql('DROP TABLE photos');
        $this->addSql('DROP TABLE groups');
    }
}
