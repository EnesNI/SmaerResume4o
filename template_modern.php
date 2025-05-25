<?php
// Modern Professional Template
$template_data = $_GET['data'] ?? [];
$preview_mode = isset($_GET['preview']);

// Sample data for preview
if ($preview_mode) {
    $template_data = [
        'full_name' => 'John Smith',
        'email' => 'john.smith@email.com',
        'phone' => '+1 (555) 123-4567',
        'location' => 'San Francisco, CA',
        'linkedin' => 'linkedin.com/in/johnsmith',
        'website' => 'johnsmith.dev',
        'summary' => 'Experienced Software Engineer with 5+ years of expertise in full-stack development, specializing in React, Node.js, and cloud technologies. Proven track record of delivering scalable solutions and leading cross-functional teams.',
        'experience' => [
            [
                'title' => 'Senior Software Engineer',
                'company' => 'Tech Solutions Inc.',
                'duration' => '2022 - Present',
                'description' => 'Led development of microservices architecture serving 1M+ users. Implemented CI/CD pipelines reducing deployment time by 60%.'
            ],
            [
                'title' => 'Software Engineer',
                'company' => 'StartupXYZ',
                'duration' => '2020 - 2022',
                'description' => 'Developed responsive web applications using React and Node.js. Collaborated with design team to improve user experience.'
            ]
        ],
        'education' => [
            [
                'degree' => 'Bachelor of Science in Computer Science',
                'school' => 'University of California, Berkeley',
                'year' => '2020'
            ]
        ],
        'skills' => ['JavaScript', 'React', 'Node.js', 'Python', 'AWS', 'Docker', 'MongoDB', 'PostgreSQL']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Professional Resume</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
        }

        .resume-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .name {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -1px;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .content {
            padding: 2rem;
        }

        .section {
            margin-bottom: 2.5rem;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #667eea;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 50px;
            height: 2px;
            background: #764ba2;
        }

        .summary {
            font-size: 1rem;
            line-height: 1.7;
            color: #555;
        }

        .experience-item, .education-item {
            margin-bottom: 1.5rem;
            padding-left: 1rem;
            border-left: 3px solid #667eea;
            position: relative;
        }

        .experience-item::before, .education-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 0.5rem;
            width: 10px;
            height: 10px;
            background: #667eea;
            border-radius: 50%;
        }

        .job-title, .degree {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .company, .school {
            font-weight: 500;
            color: #667eea;
            margin-bottom: 0.25rem;
        }

        .duration, .year {
            font-size: 0.9rem;
            color: #888;
            margin-bottom: 0.5rem;
        }

        .description {
            color: #555;
            line-height: 1.6;
        }

        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.5rem;
        }

        .skill-item {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-align: center;
            font-size: 0.9rem;
            font-weight: 500;
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
                font-size: 2rem;
            }
            .contact-info {
                flex-direction: column;
                gap: 0.5rem;
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
            <h1 class="name"><?php echo htmlspecialchars($template_data['full_name'] ?? 'Your Name'); ?></h1>
            <div class="contact-info">
                <?php if (!empty($template_data['email'])): ?>
                    <div class="contact-item">📧 <?php echo htmlspecialchars($template_data['email']); ?></div>
                <?php endif; ?>
                <?php if (!empty($template_data['phone'])): ?>
                    <div class="contact-item">📱 <?php echo htmlspecialchars($template_data['phone']); ?></div>
                <?php endif; ?>
                <?php if (!empty($template_data['location'])): ?>
                    <div class="contact-item">📍 <?php echo htmlspecialchars($template_data['location']); ?></div>
                <?php endif; ?>
                <?php if (!empty($template_data['linkedin'])): ?>
                    <div class="contact-item">💼 <?php echo htmlspecialchars($template_data['linkedin']); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="content">
            <?php if (!empty($template_data['summary'])): ?>
            <div class="section">
                <h2 class="section-title">Professional Summary</h2>
                <p class="summary"><?php echo htmlspecialchars($template_data['summary']); ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($template_data['experience'])): ?>
            <div class="section">
                <h2 class="section-title">Professional Experience</h2>
                <?php foreach ($template_data['experience'] as $job): ?>
                <div class="experience-item">
                    <div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
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
                <div class="skills-grid">
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
