<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240529200051 extends AbstractMigration {
    public function getDescription() : string {
        return '';
    }

    public function up(Schema $schema) : void {
        // this up() migration is auto-generated, please modify it to your needs
        $current = '="/circus/editor/upload/image/';
        $replace = '="/editor/upload/image/';
        $this->addSql('UPDATE nines_blog_page SET excerpt=REPLACE(excerpt, \'' . $current . '\', \'' . $replace . '\')');
        $this->addSql('UPDATE nines_blog_page SET content=REPLACE(content, \'' . $current . '\', \'' . $replace . '\')');
        $this->addSql('UPDATE nines_blog_post SET excerpt=REPLACE(excerpt, \'' . $current . '\', \'' . $replace . '\')');
        $this->addSql('UPDATE nines_blog_post SET content=REPLACE(content, \'' . $current . '\', \'' . $replace . '\')');
    }

    public function down(Schema $schema) : void {
        // this down() migration is auto-generated, please modify it to your needs
        $current = '="/editor/upload/image/';
        $replace = '="/circus/editor/upload/image/';
        $this->addSql('UPDATE nines_blog_page SET excerpt=REPLACE(excerpt, \'' . $current . '\', \'' . $replace . '\')');
        $this->addSql('UPDATE nines_blog_page SET content=REPLACE(content, \'' . $current . '\', \'' . $replace . '\')');
        $this->addSql('UPDATE nines_blog_post SET excerpt=REPLACE(excerpt, \'' . $current . '\', \'' . $replace . '\')');
        $this->addSql('UPDATE nines_blog_post SET content=REPLACE(content, \'' . $current . '\', \'' . $replace . '\')');
    }
}
