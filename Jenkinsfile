pipeline {
    agent any

    stages {

        stage('Get Source') {
            steps {
                git url: 'https://github.com/accerq/i-educar.git', branch: 'main'
            }
        }

        stage('Docker Build') {
            steps {
                script {
                    dockerapp = docker.build("accerq/ieducar-php:2.8"),
                        '-f docker/php/Dockerfile .'
                    dockerapp = docker.build("accerq/ieducar-php:latest"),
                        '-f docker/php/Dockerfile .'
                    dockerapp = docker.build("accerq/ieducar-nginx:2.8"),
                        '-f docker/nginx/Dockerfile .'
                    dockerapp = docker.build("accerq/ieducar-nginx:latest"),
                        '-f docker/nginx/Dockerfile .'
                }
            }
        }

        stage('Docker Push Image') {
            steps {
                script {
                    docker.withRegistry('https://registry.hub.docker.com', 'dockerhub') {
                    dockerapp.push('accerq/ieducar-php:latest')
                    dockerapp.push("accerq/ieducar-php:2.8")
                    }
                }
            }
        }
    }
}