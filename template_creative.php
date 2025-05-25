<?php
// Creative Design Template
$template_data = $_GET['data'] ?? [];
$preview_mode = isset($_GET['preview']);

// Sample data for preview
if ($preview_mode) {
    $template_data = [
        'full_name' => 'Sarah Johnson',
        'title' => 'Creative Designer',
        'email' => 'sarah.johnson@email.com',
        'phone' => '+1 (555) 987-6543',
        'location' => 'New York, NY',
        'portfolio' => 'sarahjohnson.design',
        'summary' => 'Passionate Creative Designer with 4+ years of experience in brand identity, digital design, and user experience. Specialized in creating compelling visual narratives that drive engagement and conversion.',
        'experience' => [
            [
                'title' => 'Senior Graphic Designer',
                'company' => 'Creative Agency Pro',
                'duration' => '2021 - Present',
                'description' => 'Lead designer for major brand campaigns. Increased client satisfaction by 40% through innovative design solutions.'
            ],
            [
                'title' => 'UI/UX Designer',
                'company' => 'Digital Studio',
                'duration' => '2019 - 2021',
                'description' => 'Designed user interfaces for mobile and web applications. Improved user engagement by 35% through intuitive design.'
            ]
        ],
        'education' => [
            [
                'degree' => 'Bachelor of Fine Arts in Graphic Design',
                'school' => 'Parsons School of Design',
                'year' => '2019'
            ]
        ],
        'skills' => ['Adobe Creative Suite', 'Figma', 'Sketch', 'Branding', 'Typography', 'UI/UX Design', 'Photography', 'Illustration']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creative Design Resume</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
        }

        .resume-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }

        .resume-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .name {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: -2px;
        }

        .job-title {
            font-size: 1.3rem;
            font-weight: 300;
            margin-bottom: 1.5rem;
            opacity: 0.9;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
        }

        .contact-icon {
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .content {
            padding: 2.5rem;
        }

        .section {
            margin-bottom: 3rem;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #ff6b6b, #feca57);
            border-radius: 2px;
        }

        .summary {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #ff6b6b;
        }

        .experience-item, .education-item {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            position: relative;
            border-left: 4px solid #48dbfb;
        }

        .job-title-item, .degree {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .company, .school {
            font-weight: 600;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .duration, .year {
            background: #667eea;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .description {
            color: #555;
            line-height: 1.7;
        }

        .skills-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .skill-item {
            background: linear-gradient(135deg, #ff6b6b, #feca57);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 25px;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
            transition: transform 0.3s ease;
        }

        .skill-item:nth-child(even) {
            background: linear-gradient(135deg, #48dbfb, #0abde3);
            box-shadow: 0 4px 15px rgba(72, 219, 251, 0.3);
        }

        .skill-item:nth-child(3n) {
            background: linear-gradient(135deg, #ff9ff3, #f368e0);
            box-shadow: 0 4px 15px rgba(255, 159, 243, 0.3);
        }

        @media print {
            body {
                background: white;
            }
            .resume-container {
                box-shadow: none;
                margin: 0;
                border-radius: 0;
            }
        }

        @media (max-width: 768px) {
            .resume-container {
                margin: 1rem;
            }
            .header {
                padding: 2rem 1rem;
            }
            .name {
                font-size: 2.2rem;
            }
            .contact-grid {
                grid-template-columns: 1fr;
            }
            .content {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="resume-container">
        <div class="header">
            <div class="header-content">
                <h1 class="name"><?php echo htmlspecialchars($template_data['full_name'] ?? 'Your Name'); ?></h1>
                <div class="job-title"><?php echo htmlspecialchars($template_data['title'] ?? 'Your Title'); ?></div>
                <div class="contact-grid">
                    <?php if (!empty($template_data['email'])): ?>
                        <div class="contact-item">
                            <div class="contact-icon">✉</div>
                            <?php echo htmlspecialchars($template_data['email']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($template_data['phone'])): ?>
                        <div class="contact-item">
                            <div class="contact-icon">📱</div>
                            <?php echo htmlspecialchars($template_data['phone']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($template_data['location'])): ?>
                        <div class="contact-item">
                            <div class="contact-icon">📍</div>
                            <?php echo htmlspecialchars($template_data['location']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($template_data['portfolio'])): ?>
                        <div class="contact-item">
                            <div class="contact-icon">🌐</div>
                            <?php echo htmlspecialchars($template_data['portfolio']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="content">
            <?php if (!empty($template_data['summary'])): ?>
            <div class="section">
                <h2 class="section-title">About Me</h2>
                <p class="summary"><?php echo htmlspecialchars($template_data['summary']); ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($template_data['experience'])): ?>
            <div class="section">
                <h2 class="section-title">Experience</h2>
                <?php foreach ($template_data['experience'] as $job): ?>
                <div class="experience-item">
                    <div class="job-title-item"><?php echo htmlspecialchars($job['title']); ?></div>
                    <div class="company"><?php echo htmlspecialchars($job['company']); ?></div>
                    <div class="duration"><?php echo htmlspecialchars($job['duration']); ?></div>
                    <div class="description"><?php echo htmlspecialchars($job['description']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($template_data['education'])): ?>
            <div class="section">
                <h2 class="section-title">Education</h2>
                <?php foreach ($template_data['education'] as $edu): ?>
                <div class="education-item">
                    <div class="degree"><?php echo htmlspecialchars($edu['degree']); ?></div>
                    <div class="school"><?php echo htmlspecialchars($edu['school']); ?></div>
                    <div class="year"><?php echo htmlspecialchars($edu['year']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($template_data['skills'])): ?>
            <div class="section">
                <h2 class="section-title">Skills</h2>
                <div class="skills-container">
                    <?php foreach ($template_data['skills'] as $skill): ?>
                    <div class="skill-item"><?php echo htmlspecialchars($skill); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
