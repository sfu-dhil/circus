<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220307213915 extends AbstractMigration {
    public function getDescription() : string {
        return '';
    }

    public function up(Schema $schema) : void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment_note DROP FOREIGN KEY FK_E98B58F8F8697D13');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C6BF700BD');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE comment_note');
        $this->addSql('DROP TABLE comment_status');
        $this->addSql('DROP TABLE element');
    }

    public function down(Schema $schema) : void {
        $this->throwIrreversibleMigrationException();
    }
}
