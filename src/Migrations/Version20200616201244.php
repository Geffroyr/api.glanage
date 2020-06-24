<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200616201244 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, agriculteur_id INT NOT NULL, lieu_id INT NOT NULL, date DATE NOT NULL, INDEX IDX_B26681E7EBB810E (agriculteur_id), UNIQUE INDEX UNIQ_B26681E6AB213CC (lieu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement_recuperateur (id INT AUTO_INCREMENT NOT NULL, evenement_id INT NOT NULL, recuperateur_id INT NOT NULL, legume_id INT NOT NULL, volume INT NOT NULL, INDEX IDX_6F8A1587FD02F13 (evenement_id), INDEX IDX_6F8A15875C456E00 (recuperateur_id), INDEX IDX_6F8A158725F18E37 (legume_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement_legume (id INT AUTO_INCREMENT NOT NULL, evenement_id INT NOT NULL, legume_id INT NOT NULL, volume INT NOT NULL, INDEX IDX_3593A301FD02F13 (evenement_id), INDEX IDX_3593A30125F18E37 (legume_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rendezvous (id INT AUTO_INCREMENT NOT NULL, evenement_id INT NOT NULL, heure TIME NOT NULL, description VARCHAR(100) NOT NULL, INDEX IDX_C09A9BA8FD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deroulement (id INT AUTO_INCREMENT NOT NULL, evenement_id INT NOT NULL, heure TIME NOT NULL, description VARCHAR(100) NOT NULL, INDEX IDX_D028DFAFFD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE agriculteur (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, lieu_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, perimetre INT DEFAULT NULL, enabled TINYINT(1) NOT NULL, utype VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3F85E0677 (username), INDEX IDX_1D1C63B36AB213CC (lieu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recuperateur (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `admin` (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE legume (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lieu (id INT AUTO_INCREMENT NOT NULL, code_postal VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, commune VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE glaneur (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement_glaneur (id INT AUTO_INCREMENT NOT NULL, evenement_id INT NOT NULL, glaneur_id INT NOT NULL, effectif INT NOT NULL, INDEX IDX_72BA3F4DFD02F13 (evenement_id), INDEX IDX_72BA3F4D9C131731 (glaneur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE validate_email (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, token VARCHAR(255) NOT NULL, requested_at DATETIME NOT NULL, INDEX IDX_A85AF755FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE actualite (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, lien VARCHAR(255) NOT NULL, date DATE NOT NULL, contenu VARCHAR(4096) NOT NULL, images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E7EBB810E FOREIGN KEY (agriculteur_id) REFERENCES agriculteur (id)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E6AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id)');
        $this->addSql('ALTER TABLE evenement_recuperateur ADD CONSTRAINT FK_6F8A1587FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE evenement_recuperateur ADD CONSTRAINT FK_6F8A15875C456E00 FOREIGN KEY (recuperateur_id) REFERENCES recuperateur (id)');
        $this->addSql('ALTER TABLE evenement_recuperateur ADD CONSTRAINT FK_6F8A158725F18E37 FOREIGN KEY (legume_id) REFERENCES legume (id)');
        $this->addSql('ALTER TABLE evenement_legume ADD CONSTRAINT FK_3593A301FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE evenement_legume ADD CONSTRAINT FK_3593A30125F18E37 FOREIGN KEY (legume_id) REFERENCES legume (id)');
        $this->addSql('ALTER TABLE rendezvous ADD CONSTRAINT FK_C09A9BA8FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE deroulement ADD CONSTRAINT FK_D028DFAFFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE agriculteur ADD CONSTRAINT FK_2366443BBF396750 FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B36AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id)');
        $this->addSql('ALTER TABLE recuperateur ADD CONSTRAINT FK_C3A779D1BF396750 FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `admin` ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE glaneur ADD CONSTRAINT FK_71BF2891BF396750 FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement_glaneur ADD CONSTRAINT FK_72BA3F4DFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE evenement_glaneur ADD CONSTRAINT FK_72BA3F4D9C131731 FOREIGN KEY (glaneur_id) REFERENCES glaneur (id)');
        $this->addSql('ALTER TABLE validate_email ADD CONSTRAINT FK_A85AF755FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE evenement_recuperateur DROP FOREIGN KEY FK_6F8A1587FD02F13');
        $this->addSql('ALTER TABLE evenement_legume DROP FOREIGN KEY FK_3593A301FD02F13');
        $this->addSql('ALTER TABLE rendezvous DROP FOREIGN KEY FK_C09A9BA8FD02F13');
        $this->addSql('ALTER TABLE deroulement DROP FOREIGN KEY FK_D028DFAFFD02F13');
        $this->addSql('ALTER TABLE evenement_glaneur DROP FOREIGN KEY FK_72BA3F4DFD02F13');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E7EBB810E');
        $this->addSql('ALTER TABLE agriculteur DROP FOREIGN KEY FK_2366443BBF396750');
        $this->addSql('ALTER TABLE recuperateur DROP FOREIGN KEY FK_C3A779D1BF396750');
        $this->addSql('ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE glaneur DROP FOREIGN KEY FK_71BF2891BF396750');
        $this->addSql('ALTER TABLE validate_email DROP FOREIGN KEY FK_A85AF755FB88E14F');
        $this->addSql('ALTER TABLE evenement_recuperateur DROP FOREIGN KEY FK_6F8A15875C456E00');
        $this->addSql('ALTER TABLE evenement_recuperateur DROP FOREIGN KEY FK_6F8A158725F18E37');
        $this->addSql('ALTER TABLE evenement_legume DROP FOREIGN KEY FK_3593A30125F18E37');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E6AB213CC');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B36AB213CC');
        $this->addSql('ALTER TABLE evenement_glaneur DROP FOREIGN KEY FK_72BA3F4D9C131731');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE evenement_recuperateur');
        $this->addSql('DROP TABLE evenement_legume');
        $this->addSql('DROP TABLE rendezvous');
        $this->addSql('DROP TABLE deroulement');
        $this->addSql('DROP TABLE agriculteur');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE recuperateur');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE legume');
        $this->addSql('DROP TABLE lieu');
        $this->addSql('DROP TABLE glaneur');
        $this->addSql('DROP TABLE evenement_glaneur');
        $this->addSql('DROP TABLE validate_email');
        $this->addSql('DROP TABLE actualite');
    }
}
