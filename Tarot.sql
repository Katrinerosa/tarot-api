-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Vært: localhost:8889
-- Genereringstid: 31. 10 2025 kl. 11:35:42
-- Serverversion: 8.0.40
-- PHP-version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Tarot`
--

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `cards`
--

CREATE TABLE `cards` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `arcana` varchar(20) NOT NULL COMMENT '\r\n“Major” eller “Minor”\r\n',
  `suit` varchar(20) NOT NULL,
  `number` int NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `upright_meaning` text NOT NULL,
  `reversed_meaning` text NOT NULL,
  `love_meaning` text NOT NULL,
  `career_meaning` text NOT NULL,
  `spiritual_meaning` text NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `element` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Data dump for tabellen `cards`
--

INSERT INTO `cards` (`id`, `name`, `arcana`, `suit`, `number`, `description`, `upright_meaning`, `reversed_meaning`, `love_meaning`, `career_meaning`, `spiritual_meaning`, `image_url`, `keywords`, `element`) VALUES
(1, 'The Magician', 'Major', '', 1, 'The Magician is the bridge between the spiritual and the physical world. He channels divine energy into form, turning vision into reality through focused will and skill. Standing before his altar, he holds one hand toward the heavens and the other toward the earth — a reminder that creation begins with intention, but is realized through action. The tools on the table — the cup, the sword, the wand, and the pentacle — represent mastery over the elements and the power to manifest.', 'Power, confidence, concentration, action.\r\n', 'Manipulation, confusion, poor planning, wasted energy.\r\n', 'In love readings, The Magician represents charm, confidence, and the ability to attract what you desire. It signals the power of intention in relationships — you can shape your love life through clarity and honest communication. When reversed, it can warn of manipulation or false appearances, reminding you to see beyond the illusion.', 'In career readings, The Magician points to new beginnings, creative ideas, and confidence in your skills. You have all the tools to turn ambition into achievement. It’s a reminder to act — not just plan. Reversed, it can indicate poor organization or self-doubt holding you back from success.', 'Spiritually, The Magician symbolizes alignment between the divine and the human will. It calls you to channel higher energy into tangible purpose. It’s the moment when you realize your thoughts shape your reality. Reversed, it can suggest disconnection, ego misuse, or spiritual bypassing — using magic without meaning.', 'images/the_magician.jpg\r\n', 'manifestation, power, skill, creation\r\n', 'Air'),
(2, 'The fool', 'Major', '', 0, 'The Fool represents new beginnings, innocence, and a leap of faith into the unknown. He trusts the journey, even when he can’t see where it leads.', 'Innocence, freedom, adventure, potential.', ' Recklessness, fear, hesitation, poor judgement.', 'n love, The Fool brings excitement, spontaneity, and the spark of something new — but also a warning not to ignore red flags or take blind risks.', ' In career, The Fool points to new paths and opportunities. It’s a sign to trust your instincts, even if the next step isn’t clear.', 'Spiritually, The Fool invites you to surrender control and embrace faith. The path unfolds as you move forward.', 'images/the_fool.jpg', 'beginnings, innocence, freedom, faith, potential', 'Air'),
(3, '\r\n\r\nThe High Priestess', 'Major', '', 2, 'The High Priestess embodies mystery, intuition, and hidden knowledge. She sits between the pillars of light and darkness, guarding the veil between the conscious and the subconscious. She asks you to listen, not act — to trust what your inner voice already knows.', 'ntuition, mystery, divine feminine, inner wisdom.', 'Secrets, confusion, repression, ignoring intuition.', 'In love readings, The High Priestess brings emotional depth and mystery. A relationship may be developing beneath the surface. Trust your instincts — not everything is meant to be spoken yet.', 'In career, this card advises patience and discernment. Observe quietly before making decisions; knowledge and timing are your power.', 'Spiritually, The High Priestess represents the inner temple — the quiet knowing of the soul. Meditation, dreams, and intuition are your guides.', 'images/the_high_priestess.jpg\r\n', 'intuition, mystery, inner voice, secrets, wisdom', 'Water'),
(4, 'The Empress', 'Major', '', 3, 'The Empress embodies creation, abundance, and nurturing energy. She represents growth, beauty, and the fertile ground of ideas made real. Her presence encourages you to receive, care, and allow things to unfold naturally.', 'Abundance, nurturing, beauty, fertility, comfort.', 'Dependence, creative block, stagnation, neglect.\r\n', 'In love, The Empress brings warmth, affection, and emotional fulfillment. Relationships deepen through care and tenderness. Reversed, it can indicate smothering or emotional imbalance.', 'In career, The Empress signals creativity and productivity. Your projects are blooming — nurture them patiently. Reversed, it warns against laziness or lack of motivation.', 'Spiritually, The Empress connects you to nature and divine creation. She reminds you that self-love and gratitude are sacred practices.\r\n', 'images/the_empress.jpg', '', 'Earth'),
(5, 'The Emperor', 'Major', '', 4, 'The Emperor stands for authority, structure, and discipline. He represents leadership and the power to turn vision into lasting order. He teaches that stability and wisdom are built through responsibility and clear boundaries.', 'Authority, structure, stability, control, leadership.', 'Domination, rigidity, inflexibility, loss of control.', 'In love, The Emperor symbolizes protection, loyalty, and structure — but can also point to emotional distance. Reversed, it may reveal power struggles or stubbornness in relationships.', 'In career, The Emperor represents ambition and mastery. Set clear goals and take decisive action. Reversed, it can mean burnout or a controlling boss.', 'Spiritually, The Emperor reminds you to bring order to your inner world. Discipline and integrity are forms of devotion.', 'images/the_emperor.jpg', 'authority, stability, leadership, structure, protection', 'Fire'),
(6, 'The Hierophant', 'Major', '', 5, 'Tradition, læring og værdier. Han repræsenterer fællesskabets visdom og strukturer, der bærer viden videre.', 'Tradition, rådgivning, læring, etik, tilhørsforhold.', 'Doktrin, stivhed, blind autoritet, brud med normer.', 'I kærlighed peger han på fælles værdier og commitment; omvendt på forventningspres eller forældede roller.', 'I karriere: mentor, certificering, formel læring; omvendt: bureaukrati eller regelrytteri.', 'Åndeligt: studér, praktisér, find en vejleder — men tænk selv.', 'images/the_hierophant.jpg', 'tradition, mentor, learning, values, community', 'Earth'),
(7, 'The Lovers', 'Major', '', 6, 'Valg, relationer og alignment. Handler om at vælge med både hjerte og integritet.', 'Kærlighed, harmoni, valg, partnerskab, alignment.', 'Ubeslutsomhed, dissonans, værdikonflikt, fristelse.', 'I kærlighed: gensidighed og ærlighed; omvendt: misalignment eller svære valg.', 'I karriere: samarbejde og værdimatch; omvendt: kompromiser der koster for meget.', 'Åndeligt: integrér hoved og hjerte — vælg med samvittighed.', 'images/the_lovers.jpg', 'love, choice, harmony, partnership, alignment', 'Air'),
(8, 'Ace of Cups', 'Minor', 'Cups', 1, 'Begyndelsen på følelsesmæssig opvågnen. Nye forbindelser, glæde og åbenhed flyder frit.', 'New feelings, love, compassion, joy.', 'Emotional block, emptiness, repression.', 'I kærlighed: nyt forhold eller fornyet nærhed. Omvendt: følelsesmæssig lukkethed.', 'I karriere: kreativ inspiration og samarbejde. Omvendt: udbrændthed eller ligegyldighed.', 'Åndeligt: hjertet åbner sig — tillad dig at føle og modtage.', 'images/ace_of_cups.jpg', 'love, compassion, new beginnings, emotion, flow', 'Water'),
(9, 'Two of Wands', 'Minor', 'Wands', 2, 'Planlægning og udsyn. Et øjeblik af forberedelse før næste skridt.', 'Future vision, decisions, planning, progress.', 'Lack of planning, fear of change, hesitation.', 'I kærlighed: vil du tage næste skridt eller forblive tryg? Omvendt: tøven eller frygt for forandring.', 'I karriere: strategisk planlægning og mod til at udvide. Omvendt: stagnation.', 'Åndeligt: se horisonten — tro på, at du kan skabe din vej.', 'images/two_of_wands.jpg', 'vision, planning, decision, expansion, courage', 'Fire'),
(10, 'Ten of Swords', 'Minor', 'Swords', 10, 'Afslutning og forløsning efter smerte. Et lavpunkt, men også begyndelsen på healing.', 'Endings, surrender, closure, lessons learned.', 'Betrayal, collapse, victim mentality, burnout.', 'I kærlighed: et forhold der har nået sin grænse. Omvendt: tid til at give slip og heale.', 'I karriere: udmattelse eller nederlag — men en ny begyndelse venter. Omvendt: rejser sig igen.', 'Åndeligt: mørket før daggry — tro på genfødsel.', 'images/ten_of_swords.jpg', 'endings, release, healing, closure, surrender', 'Air'),
(11, 'the Tower', 'Major', 'Major Arcana', 16, 'chaos', 'chaos', 'chaos', 'chaos', 'chaos', 'chaos', 'https://upload.wikimedia.org/wikipedia/commons/5/53/RWS_Tarot_16_Tower.jpg', 'Disaster, Chaos, Lightning strikes', 'Fire'),
(12, 'Temperance', 'Major', 'Major Arcana', 14, 'Everything about this card represents balance, the perfect harmony that comes from the union of dualities. Her advice is to test any new waters, before jumping into the deep end.', 'In moments where there is anxiety or great stress, you have been able to remain calm throughout. You are a person who has mastered the art of not letting things get to you, and this allows you to achieve much progress in all areas you seek out to explore. The Temperance tarot card suggests moderation and balance, coupled with a lot of patience. Where this card appears, there is the suggestion that extremity in any situation is to be avoided.', 'Lack of a long-term plan or vision may also be the Temperance reversal meaning. This creates a lack of purpose for you, leaving you feeling lopsided as you search here and there for what you should be doing.', 'careful and considerate with love, being patient with love or loverAre you too pushy with potential partners? Or instead, are you too reserved?', 'success from patience and moderation, steady and slow progress', 'In terms of your own feelings, Temperance urges you to find equilibrium within yourself. You may crave peace and stability as you navigate your emotions. This card encourages you to blend different aspects of your life harmoniously, prompting reflection on what truly brings you joy while keeping an open heart toward the possibilities this relationship holds.', 'https://upload.wikimedia.org/wikipedia/commons/f/f8/RWS_Tarot_14_Temperance.jpg', 'balance, peace, patience, moderation, calm, tranquillity, harmony, serenity', 'Fire'),
(13, 'The Devil', 'Major', 'Major Arcana', 15, 'codenpendency', 'codependency', 'codependency', 'codependency', 'codependency', 'codependency', 'https://upload.wikimedia.org/wikipedia/commons/5/55/RWS_Tarot_15_Devil.jpg', 'slave, afhængighed, codependency', 'Earth'),
(16, 'The Star', 'Major', '', 17, 'It is a ‘star’, of course, because it is there to guide you. To help you navigate these tricky waters. Like a sailor following the constellations, that inner light inside you will guide you home to yourself. The Star brings you the healing, steadying message that you are still you. Whatever has changed, whatever you have been through, that essence of you remains, strong and bright, burning within. (It can also represent a person who has passed away, reminding you that their essence and energy lives on.)\r\n\r\n', '', '', '', '', '', 'https://upload.wikimedia.org/wikipedia/commons/d/db/RWS_Tarot_17_Star.jpg', 'Hope\r\nLove and support\r\nComing home to yourself\r\nSelf-care\r\nHealing\r\nBeing true to yourself\r\nBeing guided by your intuition\r\nIntegrity, honesty\r\nA positive new start or new vision', 'Air'),
(17, 'The Moon', 'Major', '', 18, 'All is not what it seems today and you may not get to the bottom of it. Strongly linked to intuition, the Moon creates a sense of knowing rather than hard cold facts. That can be more than a little disconcerting because overactive imaginations can concoct all kinds of betrayals and misdeeds carried out by others. This clandestine, cloak and dagger ', 'The Moon suggests, hints & sometimes reveals but it doesn’t do much else. That means you can ride out the storm of uncertainty until you know more over the coming days. Then you can start gathering intel and begin the process of confirming or denying what your gut, or the universe, may have been trying to tell you.', 'Secrets being revealed, what was hidden is now becoming visible, deceptions are seen, mysteries unveiled, unusual dreams, psychic insights you may not understand, insomnia or unusual sleeping patterns, irrational thoughts/behaviours.', 'Undercover agents, detectives, private investigators, mystery shoppers, actors, illusionists, mediums, psychics, travelers, spiritual seekers, shaman, refuge staff, veterinarians.\r\n', 'sleep on it', 'pray', 'https://upload.wikimedia.org/wikipedia/commons/7/7f/RWS_Tarot_18_Moon.jpg', 'Dreams but also nightmares, illusion, hidden things – particularly enemies, insecurity, mystery, falsehoods, visions,', 'Water'),
(24, 'The Sun', 'Major', '', 19, 'Joy, success, and enlightenment.', 'happiness, clarity, vitality', 'temporary doubt, fatigue, confusion', '', '', '', 'https://upload.wikimedia.org/wikipedia/commons/1/17/RWS_Tarot_19_Sun.jpg', 'joy, vitality, warmth', 'Fire'),
(25, 'Judgement', 'Major', '', 20, 'The Sleeping Dead are emerging from crypts or graves, calling back to the Revelation 20, where the sea gives up its dead. There are snow-covered mountains in the background indicating a winter theme, similar to The Hermit, as a symbolical ending.\r\n\r\n', 'Judgement', 'Judgement', 'Judgement', 'Judgement', 'Judgement', 'https://upload.wikimedia.org/wikipedia/commons/d/dd/RWS_Tarot_20_Judgement.jpg', 'Judgement', 'Fire');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `card_pairs`
--

CREATE TABLE `card_pairs` (
  `id` int NOT NULL,
  `card1_id` int NOT NULL,
  `card1_orientation` enum('Upright','Reversed','Any') COLLATE utf8mb4_unicode_ci DEFAULT 'Any',
  `card2_id` int NOT NULL,
  `card2_orientation` enum('Upright','Reversed','Any') COLLATE utf8mb4_unicode_ci DEFAULT 'Any',
  `context` enum('General','Love','Career','Spiritual') COLLATE utf8mb4_unicode_ci DEFAULT 'General',
  `meaning` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Data dump for tabellen `card_pairs`
--

INSERT INTO `card_pairs` (`id`, `card1_id`, `card1_orientation`, `card2_id`, `card2_orientation`, `context`, `meaning`) VALUES
(1, 1, 'Upright', 2, 'Reversed', 'General', 'Magician + Fool(R): fokus og kunnen møder uforberedthed — brug evnerne, men undgå dumdristighed.');

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `card_pairs`
--
ALTER TABLE `card_pairs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_pair` (`card1_id`,`card1_orientation`,`card2_id`,`card2_orientation`,`context`),
  ADD KEY `fk_pair_card2` (`card2_id`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Tilføj AUTO_INCREMENT i tabel `card_pairs`
--
ALTER TABLE `card_pairs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Begrænsninger for tabel `card_pairs`
--
ALTER TABLE `card_pairs`
  ADD CONSTRAINT `fk_pair_card1` FOREIGN KEY (`card1_id`) REFERENCES `cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pair_card2` FOREIGN KEY (`card2_id`) REFERENCES `cards` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
