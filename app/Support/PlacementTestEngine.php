<?php

namespace App\Support;

class PlacementTestEngine
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function questions(string $locale = 'tr'): array
    {
        $lang = strtolower(substr($locale, 0, 2)) === 'tr' ? 'tr' : 'en';

        return array_map(function (array $question) use ($lang): array {
            return [
                'id' => $question['id'],
                'prompt' => $question['prompt'][$lang] ?? $question['prompt']['en'],
                'options' => array_map(function (array $option) use ($lang): array {
                    return [
                        'id' => $option['id'],
                        'label' => $option['label'][$lang] ?? $option['label']['en'],
                    ];
                }, $question['options']),
            ];
        }, $this->catalog());
    }

    /**
     * @param  array<string, string>  $answers
     * @return array<string, mixed>
     */
    public function evaluate(array $answers, string $locale = 'tr'): array
    {
        $score = 0;
        $maxScore = 0;
        $answered = 0;

        foreach ($this->catalog() as $question) {
            $optionMap = [];
            foreach ($question['options'] as $option) {
                $optionMap[$option['id']] = (int) $option['score'];
            }

            $maxScore += max($optionMap ?: [0]);

            $selected = (string) ($answers[$question['id']] ?? '');
            if ($selected !== '' && array_key_exists($selected, $optionMap)) {
                $score += $optionMap[$selected];
                $answered++;
            }
        }

        $level = $this->resolveLevel($score);
        $recommendation = $this->recommendation($level, $locale);

        return [
            'score' => $score,
            'max_score' => $maxScore,
            'answered_count' => $answered,
            'question_count' => count($this->catalog()),
            'level' => $level,
            'recommended_track' => $recommendation['track'],
            'summary' => $recommendation['summary'],
            'next_step' => $recommendation['next_step'],
        ];
    }

    public function resolveLevel(int $score): string
    {
        if ($score <= 5) {
            return 'A1';
        }
        if ($score <= 10) {
            return 'A2';
        }
        if ($score <= 15) {
            return 'B1';
        }
        if ($score <= 19) {
            return 'B2';
        }
        if ($score <= 22) {
            return 'C1';
        }

        return 'C2';
    }

    /**
     * @return array{track: string, summary: string, next_step: string}
     */
    public function recommendation(string $level, string $locale = 'tr'): array
    {
        $lang = strtolower(substr($locale, 0, 2)) === 'tr' ? 'tr' : 'en';

        $map = [
            'A1' => [
                'track' => ['tr' => 'English From Scratch', 'en' => 'English From Scratch'],
                'summary' => [
                    'tr' => 'Temel yapıların üstüne sağlam bir başlangıç programı öneriyoruz.',
                    'en' => 'We recommend a strong beginner program focused on fundamentals.',
                ],
                'next_step' => [
                    'tr' => 'Haftada 2 ders + düzenli konuşma pratiği ile 8 haftalık planla başla.',
                    'en' => 'Start with 2 lessons/week plus guided speaking practice for 8 weeks.',
                ],
            ],
            'A2' => [
                'track' => ['tr' => 'General English', 'en' => 'General English'],
                'summary' => [
                    'tr' => 'Günlük iletişimde akıcılığı artırmak için Genel İngilizce öneriyoruz.',
                    'en' => 'We recommend General English to improve daily communication fluency.',
                ],
                'next_step' => [
                    'tr' => 'Kısa seviye hedefleriyle 2-3 ay içinde B1 seviyesine çıkabilirsin.',
                    'en' => 'With short goals, you can reach B1 in about 2-3 months.',
                ],
            ],
            'B1' => [
                'track' => ['tr' => 'Speaking Practice', 'en' => 'Speaking Practice'],
                'summary' => [
                    'tr' => 'Konuşma akıcılığı ve özgüven için konuşma odaklı dersler öneriyoruz.',
                    'en' => 'We recommend speaking-focused lessons for fluency and confidence.',
                ],
                'next_step' => [
                    'tr' => 'Gerçek senaryolarla düzenli pratik yaparak B2 hedefini hızlandır.',
                    'en' => 'Use regular real-life practice to accelerate your B2 target.',
                ],
            ],
            'B2' => [
                'track' => ['tr' => 'Business English', 'en' => 'Business English'],
                'summary' => [
                    'tr' => 'İş hayatı, sunum ve toplantı dili için Business English öneriyoruz.',
                    'en' => 'We recommend Business English for meetings, presentations, and work.',
                ],
                'next_step' => [
                    'tr' => 'Sektörüne uygun içerikle profesyonel iletişim seviyeni yükselt.',
                    'en' => 'Raise professional communication with industry-specific content.',
                ],
            ],
            'C1' => [
                'track' => ['tr' => 'IELTS & TOEFL', 'en' => 'IELTS & TOEFL'],
                'summary' => [
                    'tr' => 'İleri seviye için sınav ve akademik odaklı ilerleme öneriyoruz.',
                    'en' => 'For advanced learners, we recommend exam and academic progression.',
                ],
                'next_step' => [
                    'tr' => 'Hedef puana göre kişisel çalışma planı ile ilerle.',
                    'en' => 'Move forward with a personal plan tailored to your target score.',
                ],
            ],
            'C2' => [
                'track' => ['tr' => 'Advanced Speaking Coaching', 'en' => 'Advanced Speaking Coaching'],
                'summary' => [
                    'tr' => 'C2 seviyesi için ileri konuşma ve uzmanlaşma koçluğu öneriyoruz.',
                    'en' => 'For C2, we recommend advanced speaking and specialization coaching.',
                ],
                'next_step' => [
                    'tr' => 'Alanına özel geri bildirimlerle üst düzey iletişimi keskinleştir.',
                    'en' => 'Sharpen high-level communication with domain-specific feedback.',
                ],
            ],
        ];

        $row = $map[$level] ?? $map['A1'];

        return [
            'track' => $row['track'][$lang],
            'summary' => $row['summary'][$lang],
            'next_step' => $row['next_step'][$lang],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function catalog(): array
    {
        return [
            [
                'id' => 'q1',
                'prompt' => [
                    'tr' => '"I ___ to work every day." cümlesindeki doğru seçenek hangisi?',
                    'en' => 'Choose the correct option for: "I ___ to work every day."',
                ],
                'options' => [
                    ['id' => 'a', 'label' => ['tr' => 'go', 'en' => 'go'], 'score' => 3],
                    ['id' => 'b', 'label' => ['tr' => 'goes', 'en' => 'goes'], 'score' => 1],
                    ['id' => 'c', 'label' => ['tr' => 'going', 'en' => 'going'], 'score' => 0],
                    ['id' => 'd', 'label' => ['tr' => 'gone', 'en' => 'gone'], 'score' => 0],
                ],
            ],
            [
                'id' => 'q2',
                'prompt' => [
                    'tr' => '"She has lived here ___ 2019." boşluğunu tamamla.',
                    'en' => 'Complete: "She has lived here ___ 2019."',
                ],
                'options' => [
                    ['id' => 'a', 'label' => ['tr' => 'for', 'en' => 'for'], 'score' => 1],
                    ['id' => 'b', 'label' => ['tr' => 'since', 'en' => 'since'], 'score' => 3],
                    ['id' => 'c', 'label' => ['tr' => 'from', 'en' => 'from'], 'score' => 0],
                    ['id' => 'd', 'label' => ['tr' => 'during', 'en' => 'during'], 'score' => 0],
                ],
            ],
            [
                'id' => 'q3',
                'prompt' => [
                    'tr' => 'Dün yaptığın bir işi en doğru şekilde ifade eden seçenek hangisi?',
                    'en' => 'Pick the best sentence for an action you did yesterday.',
                ],
                'options' => [
                    ['id' => 'a', 'label' => ['tr' => 'I go to the gym yesterday.', 'en' => 'I go to the gym yesterday.'], 'score' => 0],
                    ['id' => 'b', 'label' => ['tr' => 'I went to the gym yesterday.', 'en' => 'I went to the gym yesterday.'], 'score' => 3],
                    ['id' => 'c', 'label' => ['tr' => 'I have go to the gym yesterday.', 'en' => 'I have go to the gym yesterday.'], 'score' => 0],
                    ['id' => 'd', 'label' => ['tr' => 'I am going to the gym yesterday.', 'en' => 'I am going to the gym yesterday.'], 'score' => 0],
                ],
            ],
            [
                'id' => 'q4',
                'prompt' => [
                    'tr' => '"If I had more time, I ___ another language." boşluğu için en uygun cevap?',
                    'en' => 'Best option for: "If I had more time, I ___ another language."',
                ],
                'options' => [
                    ['id' => 'a', 'label' => ['tr' => 'learn', 'en' => 'learn'], 'score' => 1],
                    ['id' => 'b', 'label' => ['tr' => 'will learn', 'en' => 'will learn'], 'score' => 0],
                    ['id' => 'c', 'label' => ['tr' => 'would learn', 'en' => 'would learn'], 'score' => 3],
                    ['id' => 'd', 'label' => ['tr' => 'am learning', 'en' => 'am learning'], 'score' => 0],
                ],
            ],
            [
                'id' => 'q5',
                'prompt' => [
                    'tr' => 'Aşağıdaki cümlelerden hangisi en doğal iş İngilizcesi ifadesidir?',
                    'en' => 'Which sentence is the most natural business English expression?',
                ],
                'options' => [
                    ['id' => 'a', 'label' => ['tr' => 'Please revert me soon.', 'en' => 'Please revert me soon.'], 'score' => 1],
                    ['id' => 'b', 'label' => ['tr' => 'Could you share the latest update by EOD?', 'en' => 'Could you share the latest update by EOD?'], 'score' => 3],
                    ['id' => 'c', 'label' => ['tr' => 'Send me answer quickly.', 'en' => 'Send me answer quickly.'], 'score' => 0],
                    ['id' => 'd', 'label' => ['tr' => 'I wait your response immediate.', 'en' => 'I wait your response immediate.'], 'score' => 0],
                ],
            ],
            [
                'id' => 'q6',
                'prompt' => [
                    'tr' => '"The project ___ by Friday." cümlesini tamamla.',
                    'en' => 'Complete: "The project ___ by Friday."',
                ],
                'options' => [
                    ['id' => 'a', 'label' => ['tr' => 'will complete', 'en' => 'will complete'], 'score' => 0],
                    ['id' => 'b', 'label' => ['tr' => 'will be completed', 'en' => 'will be completed'], 'score' => 3],
                    ['id' => 'c', 'label' => ['tr' => 'completes', 'en' => 'completes'], 'score' => 0],
                    ['id' => 'd', 'label' => ['tr' => 'is complete', 'en' => 'is complete'], 'score' => 1],
                ],
            ],
            [
                'id' => 'q7',
                'prompt' => [
                    'tr' => 'Hangisi ileri seviye bağlaç kullanımına örnektir?',
                    'en' => 'Which sentence shows advanced linker usage?',
                ],
                'options' => [
                    ['id' => 'a', 'label' => ['tr' => 'I was tired but I finished.', 'en' => 'I was tired but I finished.'], 'score' => 1],
                    ['id' => 'b', 'label' => ['tr' => 'Although I was exhausted, I still met the deadline.', 'en' => 'Although I was exhausted, I still met the deadline.'], 'score' => 3],
                    ['id' => 'c', 'label' => ['tr' => 'Because tired, I finish.', 'en' => 'Because tired, I finish.'], 'score' => 0],
                    ['id' => 'd', 'label' => ['tr' => 'I tired and finishedly done.', 'en' => 'I tired and finishedly done.'], 'score' => 0],
                ],
            ],
            [
                'id' => 'q8',
                'prompt' => [
                    'tr' => 'Bu cümlelerden hangisi telaffuz ve akıcılık hedefi için en uygun konuşma çıktısıdır?',
                    'en' => 'Which output sounds best for fluency and speaking control?',
                ],
                'options' => [
                    ['id' => 'a', 'label' => ['tr' => 'I am agree with that opinion.', 'en' => 'I am agree with that opinion.'], 'score' => 0],
                    ['id' => 'b', 'label' => ['tr' => 'I agree with that point, however we should examine the risks first.', 'en' => 'I agree with that point, however we should examine the risks first.'], 'score' => 3],
                    ['id' => 'c', 'label' => ['tr' => 'I agree but not because reasons.', 'en' => 'I agree but not because reasons.'], 'score' => 0],
                    ['id' => 'd', 'label' => ['tr' => 'Agreeing is my idea for this.', 'en' => 'Agreeing is my idea for this.'], 'score' => 1],
                ],
            ],
        ];
    }
}

