<?php

namespace PnDev\ContactForm\Services;

class CaptchaService
{
    /**
     * Determine le type de challenge actuel (change tous les N jours).
     */
    public function getCurrentChallengeType(): int
    {
        $rotationDays = config('contact-form.captcha.rotation_days', 3);
        $cycle = (int) floor(time() / (86400 * $rotationDays));

        return $cycle % 6;
    }

    /**
     * Genere un challenge CAPTCHA et stocke la reponse en session.
     */
    public function generate(): array
    {
        $type = $this->getCurrentChallengeType();
        [$question, $answer] = $this->buildChallenge($type);

        session([
            config('contact-form.captcha.session_key', 'contact_captcha') => [
                'answer'     => mb_strtolower(trim((string) $answer)),
                'created_at' => time(),
            ],
        ]);

        return ['question' => $question, 'type' => $type];
    }

    /**
     * Genere un challenge pour API (retourne un token chiffre au lieu de session).
     */
    public function generateForApi(): array
    {
        $type = $this->getCurrentChallengeType();
        [$question, $answer] = $this->buildChallenge($type);

        $token = encrypt([
            'answer'     => mb_strtolower(trim((string) $answer)),
            'expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        return ['question' => $question, 'captcha_token' => $token];
    }

    /**
     * Valide la reponse CAPTCHA via session.
     */
    public function validate(string $input): bool
    {
        $sessionKey = config('contact-form.captcha.session_key', 'contact_captcha');
        $stored = session($sessionKey);

        if (! $stored || (time() - $stored['created_at']) > 600) {
            session()->forget($sessionKey);
            return false;
        }

        $valid = mb_strtolower(trim($input)) === $stored['answer'];

        session()->forget($sessionKey);

        return $valid;
    }

    /**
     * Valide la reponse CAPTCHA via token chiffre (API).
     */
    public function validateToken(string $input, string $token): bool
    {
        try {
            $data = decrypt($token);
        } catch (\Exception $e) {
            return false;
        }

        if (! isset($data['answer'], $data['expires_at'])) {
            return false;
        }

        if (time() > $data['expires_at']) {
            return false;
        }

        return mb_strtolower(trim($input)) === $data['answer'];
    }

    /**
     * Construit un challenge selon le type.
     *
     * @return array{0: string, 1: string|int}
     */
    protected function buildChallenge(int $type): array
    {
        return match ($type) {
            0 => $this->mathChallenge(),
            1 => $this->wordChallenge(),
            2 => $this->logicChallenge(),
            3 => $this->countingChallenge(),
            4 => $this->dateChallenge(),
            5 => $this->cultureChallenge(),
            default => $this->mathChallenge(),
        };
    }

    /**
     * Type 0 : Question mathematique simple.
     */
    protected function mathChallenge(): array
    {
        $operations = [
            function () {
                $a = rand(2, 30);
                $b = rand(2, 20);
                return ["Combien font $a + $b ?", $a + $b];
            },
            function () {
                $a = rand(15, 50);
                $b = rand(2, 14);
                return ["Combien font $a - $b ?", $a - $b];
            },
            function () {
                $a = rand(2, 9);
                $b = rand(2, 9);
                return ["Combien font $a x $b ?", $a * $b];
            },
        ];

        return $operations[array_rand($operations)]();
    }

    /**
     * Type 1 : Ecrire un mot specifique.
     */
    protected function wordChallenge(): array
    {
        $words = [
            'bonjour', 'securite', 'contrat', 'signature', 'france',
            'confiance', 'document', 'accord', 'service', 'contact',
            'message', 'formulaire', 'entreprise', 'protection', 'valider',
        ];

        $word = $words[array_rand($words)];

        return ["Recopiez le mot suivant : $word", $word];
    }

    /**
     * Type 2 : Question de logique simple.
     */
    protected function logicChallenge(): array
    {
        $questions = [
            ['Quelle est la couleur du ciel par beau temps ?', 'bleu'],
            ['Combien de jours dans une semaine ?', '7'],
            ['Quel animal miaule ?', 'chat'],
            ['Quelle saison vient apres l\'hiver ?', 'printemps'],
            ['Combien de pattes a un chien ?', '4'],
            ['Quel est le contraire de chaud ?', 'froid'],
            ['Combien de mois dans une annee ?', '12'],
            ['Quel est le contraire de grand ?', 'petit'],
            ['De quelle couleur est la neige ?', 'blanc'],
            ['Combien d\'heures dans une journee ?', '24'],
            ['Quel est le premier mois de l\'annee ?', 'janvier'],
            ['Combien font une douzaine ?', '12'],
        ];

        return $questions[array_rand($questions)];
    }

    /**
     * Type 3 : Compter des caracteres dans un mot.
     */
    protected function countingChallenge(): array
    {
        $challenges = [
            ['Combien de fois apparait la lettre A dans ABRACADABRA ?', '5'],
            ['Combien de voyelles dans BONJOUR ?', '3'],
            ['Combien de lettres dans le mot CONTACT ?', '7'],
            ['Combien de chiffres dans 47293 ?', '5'],
            ['Combien de mots dans : Le chat dort bien ?', '4'],
            ['Combien de voyelles dans SECURITE ?', '4'],
            ['Combien de lettres dans FRANCE ?', '6'],
            ['Combien de consonnes dans PARIS ?', '3'],
            ['Combien de mots dans : Je signe mon contrat ?', '4'],
            ['Combien de lettres dans SIGNATURE ?', '9'],
        ];

        return $challenges[array_rand($challenges)];
    }

    /**
     * Type 4 : Question sur la date actuelle.
     */
    protected function dateChallenge(): array
    {
        $months = [
            1 => 'janvier', 2 => 'fevrier', 3 => 'mars', 4 => 'avril',
            5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'aout',
            9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'decembre',
        ];

        $days = [
            0 => 'dimanche', 1 => 'lundi', 2 => 'mardi', 3 => 'mercredi',
            4 => 'jeudi', 5 => 'vendredi', 6 => 'samedi',
        ];

        $subType = rand(0, 2);

        return match ($subType) {
            0 => ['En quelle annee sommes-nous ?', (string) date('Y')],
            1 => ['Quel est le mois actuel ? (en lettres)', $months[(int) date('n')]],
            2 => ['Quel jour de la semaine sommes-nous ? (en lettres)', $days[(int) date('w')]],
        };
    }

    /**
     * Type 5 : Culture generale francaise.
     */
    protected function cultureChallenge(): array
    {
        $questions = [
            ['Quelle est la capitale de la France ?', 'paris'],
            ['Combien de centimes dans un euro ?', '100'],
            ['Quel fleuve traverse Paris ?', 'seine'],
            ['Combien de lettres dans l\'alphabet ?', '26'],
            ['Quelle est la monnaie de la France ?', 'euro'],
            ['Combien de continents y a-t-il ?', '7'],
            ['De quelle couleur est le drapeau blanc au milieu du drapeau francais ?', 'blanc'],
            ['Quel ocean borde la cote ouest de la France ?', 'atlantique'],
            ['Combien de minutes dans une heure ?', '60'],
            ['Combien de secondes dans une minute ?', '60'],
            ['Quel est le plus grand pays d\'Europe ?', 'russie'],
            ['Combien de cotes a un triangle ?', '3'],
        ];

        return $questions[array_rand($questions)];
    }
}
