<?php
// Executive Style Template
$template_data = $_GET['data'] ?? [];
$preview_mode = isset($_GET['preview']);

// Sample data for preview
if ($preview_mode) {
    $template_data = [
        'full_name' => 'Robert Williams',
        'title' => 'Chief Technology Officer',
        'email' => 'robert.williams@email.com',
        'phone' => '+1 (555) 234-5678',
        'location' => 'Boston, MA',
        'linkedin' => 'linkedin.com/in/robertwilliams',
        'summary' => 'Visionary technology executive with 15+ years of experience leading digital transformation initiatives at Fortune 500 companies. Proven track record of scaling engineering teams, driving innovation, and delivering enterprise solutions that generate $100M+ in revenue.',
        'experience' => [
            [
                'title' => 'Chief Technology Officer',
                'company' => 'Global Enterprises Inc.',
                'duration' => '2020 - Present',
                'description' => 'Lead technology strategy for $2B organization with 500+ engineers across 12 countries. Spearheaded cloud migration reducing infrastructure costs by 40% while improving system reliability to 99.9% uptime.'
            ],
            [
                'title' => 'VP of Engineering',
                'company' => 'TechGiant Corp',
                'duration' => '2017 - 2020',
                'description' => 'Built and managed engineering organization of 200+ developers. Launched 5 major product lines generating $50M+ annual recurring revenue. Implemented DevOps practices reducing deployment time from weeks to hours.'
            ],
            [
                'title' => 'Senior Director of Technology',
                'company' => 'Innovation Systems',
                'duration' => '2014 - 2017',
                'description' => 'Led architecture and development of enterprise platform serving 1M+ users. Established engineering best practices and mentored 50+ senior engineers. Achieved 99.95% system availability.'
            ]
        ],
        'education' => [
            [
                'degree' => 'Master of Science in Computer Science',
                'school' => 'Massachusetts Institute of Technology',
                'year' => '2008'
            ],
            [
                'degree' => 'Bachelor of Science in Electrical Engineering',
                'school' => 'Stanford University',
                'year' => '2006'
            ]
        ],
        'skills' => ['Strategic Leadership', 'Digital Transformation', 'Cloud Architecture', 'Team Building', 'Product Strategy', 'Agile Methodologies', 'Enterprise Software', 'Cybersecurity']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Style Resume</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
            color: #1a1a1a;
            background: #f8f9fa;
        }

        .resume-container {
            max-width: 850px;
            margin: 2rem auto;
            background: white;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border-radius: 0;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2c3e50 100%);
            color: white;
            padding: 3rem 3rem 2rem 3rem;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #c9b037, #f4e58c, #c9b037);
        }

        .name {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: 1px;
        }

        .executive-title {
            font-size: 1.4rem;
            font-weight: 300;
            margin-bottom: 2rem;
            color: #ecf0f1;
            letter-spacing: 0.5px;
        }

        .contact-executive {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            font-size: 0.95rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .contact-icon {
            width: 18px;
            text-align: center;
            color: #c9b037;
        }

        .content {
            padding: 3rem;
        }

        .section {
            margin-bottom: 3rem;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            padding-bottom: 0.75rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #c9b037, #f4e58c);
        }

        .executive-summary {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #2c3e50;
            text-align: justify;
            background: #f8f9fa;
            padding: 2rem;
            border-left: 4px solid #c9b037;
            margin-bottom: 1rem;
        }

        .experience-item {
            margin-bottom: 2.5rem;
            padding: 2rem;
            background: #ffffff;
            border: 1px solid #ecf0f1;
            border-left: 4px solid #2c3e50;
            position: relative;
        }

        .experience-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 2rem;
            width: 12px;
            height: 12px;
            background: #c9b037;
            border-radius: 50%;
            border: 3px solid white;
        }

        .job-title-exec {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }

        .company-exec {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .duration-exec {
            font-size: 0.95rem;
            color: #7f8c8d;
            font-style: italic;
            margin-bottom: 1rem;
        }

        .description-exec {
            color: #34495e;
            line-height: 1.7;
            font-size: 1rem;
        }

        .education-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 1.5rem;
            background: #f8f9fa;
            border-left: 4px solid #c9b037;
            margin-bottom: 1rem;
        }

        .degree-exec {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.25rem;
        }

        .school-exec {
            color: #2c3e50;
            font-style: italic;
        }

        .year-exec {
            font-weight: 600;
            color: #7f8c8d;
            font-size: 0.95rem;
        }

        .skills-executive {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .skill-item-exec {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 1rem 1.5rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 0;
            position: relative;
            overflow: hidden;
        }

        .skill-item-exec::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #c9b037, #f4e58c);
        }

        @media print {
            body {
                background: white;
            }
            .resume-container {
                box-shadow: none;
                margin: 0;
            }
        }

        @media (max-width: 768px) {
            .resume-container {
                margin: 1rem;
            }
            .header {
                padding: 2rem 1.5rem;
            }
            .name {
                font-size: 2.2rem;
            }
            .contact-executive {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            .content {
                padding: 2rem 1.5rem;
            }
            .education-item {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="resume-container">
        <div class="header">
            <h1 class="name"><?php echo htmlspecialchars($template_data['full_name'] ?? 'Your Name'); ?></h1>
            <div class="executive-title"><?php echo htmlspecialchars($template_data['title'] ?? 'Executive Title'); ?></div>
            <div class="contact-executive">
                <?php if (!empty($template_data['email'])): ?>
                    <div class="contact-item">
                        <div class="contact-icon">✉</div>
                        <?php echo htmlspecialchars($template_data['email']); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($template_data['phone'])): ?>
                    <div class="contact-item">
                        <div class="contact-icon">☎</div>
                        <?php echo htmlspecialchars($template_data['phone']); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($template_data['location'])): ?>
                    <div class="contact-item">
                        <div class="contact-icon">📍</div>
                        <?php echo htmlspecialchars($template_data['location']); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($template_data['linkedin'])): ?>
                    <div class="contact-item">
                        <div class="contact-icon">💼</div>
                        <?php echo htmlspecialchars($template_data['linkedin']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="content">
            <?php if (!empty($template_data['summary'])): ?>
            <div class="section">
                <h2 class="section-title">Executive Summary</h2>
                <p class="executive-summary"><?php echo htmlspecialchars($template_data['summary']); ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($template_data['experience'])): ?>
            <div class="section">
                <h2 class="section-title">Professional Experience</h2>
                <?php foreach ($template_data['experience'] as $job): ?>
                <div class="experience-item">
                    <div class="job-title-exec"><?php echo htmlspecialchars($job['title']); ?></div>
                    <div class="company-exec"><?php echo htmlspecialchars($job['company']); ?></div>
                    <div class="duration-exec"><?php echo htmlspecialchars($job['duration']); ?></div>
                    <div class="description-exec"><?php echo htmlspecialchars($job['description']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($template_data['education'])): ?>
            <div class="section">
                <h2 class="section-title">Education</h2>
                <?php foreach ($template_data['education'] as $edu): ?>
                <div class="education-item">
                    <div>
                        <div class="degree-exec"><?php echo htmlspecialchars($edu['degree']); ?></div>
                        <div class="school-exec"><?php echo htmlspecialchars($edu['school']); ?></div>
                    </div>
                    <div class="year-exec"><?php echo htmlspecialchars($edu['year']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($template_data['skills'])): ?>
            <div class="section">
                <h2 class="section-title">Core Competencies</h2>
                <div class="skills-executive">
                    <?php foreach ($template_data['skills'] as $skill): ?>
                    <div class="skill-item-exec"><?php echo htmlspecialchars($skill); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
