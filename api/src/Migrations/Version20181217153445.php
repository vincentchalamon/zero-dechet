<?php

/*
 * This file is part of the Zero Dechet project.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Generate database schema.
 */
final class Version20181217153445 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE place (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE question (id UUID NOT NULL, quiz_id UUID NOT NULL, title VARCHAR(255) NOT NULL, urls TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6F7494E853CD175 ON question (quiz_id)');
        $this->addSql('COMMENT ON COLUMN question.urls IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE weighing (id UUID NOT NULL, user_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, total DOUBLE PRECISION NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8FEC4CFCA76ED395 ON weighing (user_id)');
        $this->addSql('CREATE TABLE shop (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, address TEXT NOT NULL, postcode VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, coordinates geometry(POINT, 0) NOT NULL, longitude DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE shop_tag (shop_id UUID NOT NULL, tag_id UUID NOT NULL, PRIMARY KEY(shop_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_907267BD4D16C4DD ON shop_tag (shop_id)');
        $this->addSql('CREATE INDEX IDX_907267BDBAD26311 ON shop_tag (tag_id)');
        $this->addSql('CREATE TABLE choice (id UUID NOT NULL, question_id UUID NOT NULL, name VARCHAR(255) NOT NULL, is_valid BOOLEAN NOT NULL, position INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C1AB5A921E27F6BF ON choice (question_id)');
        $this->addSql('CREATE TABLE password_token (id UUID NOT NULL, user_id UUID NOT NULL, token VARCHAR(50) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BEAB6C245F37A13B ON password_token (token)');
        $this->addSql('CREATE INDEX IDX_BEAB6C24A76ED395 ON password_token (user_id)');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, token VARCHAR(200) NOT NULL, email VARCHAR(200) NOT NULL, email_canonical VARCHAR(200) NOT NULL, password VARCHAR(64) NOT NULL, roles TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A0D96FBF ON "user" (email_canonical)');
        $this->addSql('COMMENT ON COLUMN "user".roles IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE user_place (user_id UUID NOT NULL, place_id UUID NOT NULL, PRIMARY KEY(user_id, place_id))');
        $this->addSql('CREATE INDEX IDX_96DFA895A76ED395 ON user_place (user_id)');
        $this->addSql('CREATE INDEX IDX_96DFA895DA6A219 ON user_place (place_id)');
        $this->addSql('CREATE TABLE quiz (id UUID NOT NULL, place_id UUID NOT NULL, position INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A412FA92DA6A219 ON quiz (place_id)');
        $this->addSql('CREATE TABLE user_quiz (id UUID NOT NULL, user_id UUID NOT NULL, quiz_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DE93B65BA76ED395 ON user_quiz (user_id)');
        $this->addSql('CREATE INDEX IDX_DE93B65B853CD175 ON user_quiz (quiz_id)');
        $this->addSql('CREATE TABLE user_quiz_choice (user_quiz_id UUID NOT NULL, choice_id UUID NOT NULL, PRIMARY KEY(user_quiz_id, choice_id))');
        $this->addSql('CREATE INDEX IDX_85F4635EDD31CF7F ON user_quiz_choice (user_quiz_id)');
        $this->addSql('CREATE INDEX IDX_85F4635E998666D1 ON user_quiz_choice (choice_id)');
        $this->addSql('CREATE TABLE tag (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE weighing ADD CONSTRAINT FK_8FEC4CFCA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shop_tag ADD CONSTRAINT FK_907267BD4D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shop_tag ADD CONSTRAINT FK_907267BDBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE choice ADD CONSTRAINT FK_C1AB5A921E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE password_token ADD CONSTRAINT FK_BEAB6C24A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_place ADD CONSTRAINT FK_96DFA895A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_place ADD CONSTRAINT FK_96DFA895DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_quiz ADD CONSTRAINT FK_DE93B65BA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_quiz ADD CONSTRAINT FK_DE93B65B853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_quiz_choice ADD CONSTRAINT FK_85F4635EDD31CF7F FOREIGN KEY (user_quiz_id) REFERENCES user_quiz (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_quiz_choice ADD CONSTRAINT FK_85F4635E998666D1 FOREIGN KEY (choice_id) REFERENCES choice (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE user_place DROP CONSTRAINT FK_96DFA895DA6A219');
        $this->addSql('ALTER TABLE quiz DROP CONSTRAINT FK_A412FA92DA6A219');
        $this->addSql('ALTER TABLE choice DROP CONSTRAINT FK_C1AB5A921E27F6BF');
        $this->addSql('ALTER TABLE shop_tag DROP CONSTRAINT FK_907267BD4D16C4DD');
        $this->addSql('ALTER TABLE user_quiz_choice DROP CONSTRAINT FK_85F4635E998666D1');
        $this->addSql('ALTER TABLE weighing DROP CONSTRAINT FK_8FEC4CFCA76ED395');
        $this->addSql('ALTER TABLE password_token DROP CONSTRAINT FK_BEAB6C24A76ED395');
        $this->addSql('ALTER TABLE user_place DROP CONSTRAINT FK_96DFA895A76ED395');
        $this->addSql('ALTER TABLE user_quiz DROP CONSTRAINT FK_DE93B65BA76ED395');
        $this->addSql('ALTER TABLE question DROP CONSTRAINT FK_B6F7494E853CD175');
        $this->addSql('ALTER TABLE user_quiz DROP CONSTRAINT FK_DE93B65B853CD175');
        $this->addSql('ALTER TABLE user_quiz_choice DROP CONSTRAINT FK_85F4635EDD31CF7F');
        $this->addSql('ALTER TABLE shop_tag DROP CONSTRAINT FK_907267BDBAD26311');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE weighing');
        $this->addSql('DROP TABLE shop');
        $this->addSql('DROP TABLE shop_tag');
        $this->addSql('DROP TABLE choice');
        $this->addSql('DROP TABLE password_token');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_place');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE user_quiz');
        $this->addSql('DROP TABLE user_quiz_choice');
        $this->addSql('DROP TABLE tag');
    }
}
