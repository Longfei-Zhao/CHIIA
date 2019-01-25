# [CHIIA-NLP](http://chiia-nlp.cecs.anu.edu.au/)
## Table of contents
1. [Introduction](#introduction)
2. [Group Member](#group-member)
3. [Stakeholders](#stakeholders)
4. [Stakeholders Exceptions](#stakeholders-expectations)
5. [Documentation](#documentation)
6. [Meeting](#meeting)
7. [Milestones](#milestones)
8. [Tools](#tools)
9. [Development Docs](#development-docs)
10. [Risk Assessment](#risk-assessment)
11. [Disclaimer & Intellectual Property Statements](#disclaimer-&-intellectual-property-statements)

## Introduction
### About CHIIA
The Chinese Investment in Australia (CHIIA) Database is a public database of Mainland Chinese direct commercial investment in Australia. The database was created and is maintained by the East Asian Bureau of Economic Research at the Crawford School of Public Policy at The Australian National University.

### About this project
CHIIA-NLP project is to identify the relevant data for CHIIA database by using natural language processing and machine learning models which calculate the likelihood between the data extracted from Factiva and the relevant datasets. Our project will automatically search for the most obvious relevant data, and save them to CHIIA database. The project will greatly reduce the workload (~300 hours per year) and costs (thousands of dollars per year) for our clients. 

## Group Member
| Name       | Role          | Email  |
| ------------- |:-------------:| :-----:|
| Hao He      | Communicator | u6153769@anu.edu.au |
| Yang Lu      | Technical Lead      |   u6274652@anu.edu.au |
| Teng Ma | Developer      |    u6123792@anu.edu.au |
| Zhoubao Mai      | Tester      |   u6118739@anu.edu.au |
| Zhe Zhang | Project Leader      |    u6128882@anu.edu.au |
| Longfei Zhao | Architect      |    u5976992@anu.edu.au |
| Yexiao Lin | Analyst     |    u6257745@anu.edu.au |

## Stakeholders
* Susan Travis, Client
* CHIIA-NLP Team
* CHIIA Researchers
* CHIIA-WS Team

## Stakeholders Expectations
| Name    | Involvement | Expectations |
| --------| :------------:| :------------:|
| Susan Travis| Project sponsor; primary project contact; responsible for CHIIA's China investment research| Expects clear communication, achievement of project and accurate data processing result|
| CHIIA Researchers | User; responsible for manually check and label partial results after program processing | Expect to have simple but clear UI, and easy approaches to work even for non-tech workers |
| CHIIA-WS Team | Responsible for workflow after preliminary data processing | Clear interface and datasets,flexible and  expandable system for further development |
| CHIIA-NLP Team | Developer; responsible for the development of NLP project | Good communication with other stakeholder, detailed documentation, accurate and efficient project development by using mature project management skills |

## Documentation
We use [GoogleDrive](https://drive.google.com/drive/folders/1tKW8WgmndtWxE8nfbuLNpcM0iiCxnppF?usp=sharing) for project documentation.

## Meeting
* Meeting with client: Every Wednesday 15:00 - 17:00 for Semester 1 && Wednesday 9：00 - 9：30 for Semester 2.
* Team meeting: Every Wednesday 17:00 - 18:00 & Every Sunday 17:00 - 18:00.

## Milestones
![Overview](https://github.com/CHIIA/CHIIA-NLP/blob/master/Docs/image/milestone.png)

| Date      | Milestone         | Week  |
| :------------- |:-------------:| :-----:|
| Semester 1 |
| 2018/2/24      | Bootcamp | Semester1 Week1 |
| 2018/3/2      | Team Building     |   Semester1 Week2 |
| 2018/3/3 | Initial Requirement Analysis      |   Semester1 Week2 |
| 2018/3/6      | Preliminary Design      |   Semester1 Week3 |
| 2018/3/8 | Design Approval      |   Semester1 Week3 |
| 2018/3/14 | Preliminary Front-end prototype      |    Semester1 Week4 |
| 2018/3/28 | First Deliverable      |    Semester1 Week6 |
| 2018/4/18 | Second Deliverable     |   Semester1 Week7 |
| 2018/4/30 | Final Prototype     |   Semester1 Week10 |
| 2018/5/3 | Showcase     |   Semester1 Week10 |
| Semester 2 |
| 2018/7/25 | Recruiting  |   Semester2 Week1 |
| 2018/7/30 | Team Reforming and Planning |   Semester2 Week2 |
| 2018/8/02 | New Requirement Analysis |   Semester2 Week2 |
| 2018/8/06 | Continuous Implementation | Semester2 Week3 |
| 2018/8/29 | Connected Old version System | Semester2 Week6 |
| Mid-Break |
| 2018/9/19 | New Version Design basically completed| Semester2 Week7 |
| 2018/10/10 | New Version Crawler basically completed| Semester2 Week10 |
| 2018/10/17 | New Version Algorithm basically completed| Semester2 Week11 |
| 2018/10/27 | New Version modules connected| Semester2 Week12 |

## Tools

* [Slack](https://chiianlp.slack.com/messages/C9H8AV2AX/) & Wechat: team communication
* [GoogleDrive](https://drive.google.com/drive/folders/1tKW8WgmndtWxE8nfbuLNpcM0iiCxnppF?usp=sharing): project documentation
* [Trello](https://trello.com/chiianlp): project notes

### Development tools

![Tools](https://github.com/CHIIA/CHIIA-NLP/blob/master/Docs/image/tools.png)

## Development Docs

* [Web Crawler](https://github.com/CHIIA/Crawler)
* [Web Server](https://github.com/CHIIA/Web)
* [Machine Learning Model](https://github.com/CHIIA/Model)


## Risk Assessment

| Risks     | Potential Costs    | Mitigation  |
| :-------------- |:----------------:| :----------------------------------------:|
| Team        | Extra uncertainty project outcomes  | Enhance team cooperation and communication, and try to avoid team member turnover.  |
| Organizational Environment       | Impact project performance  | Stabilize the organization environment, and get informed of changing organizational support in advance.   |
| Requirements        | Time and resources for useless system | Continue to communicate with clients, and get correct, clear, adequate and usable requirements.      |
| Planning and Control    | unrealistic schedules/budget, excessive schedule pressure  |  Make accurate duration estimates, and analyze what resources to commit to the corresponding development effort.    |
| User        | Project failure   | Investigate the specific user's preferences, make the appropriate UI, and keep communication with them.     |
| Project Complexity   | Impact on project performance  | Determine whether new technology is used, implement automaticity, and interact with external entities.    |
| Accuracy of classification and retrieved articles | Cannot achieve what the client anticipate (100% accuracy) | Optimize the machine learning model as much as possible. Solve the problem indirectly by ranking  |


## Disclaimer & Intellectual Property Statements
The following information forms part of the terms and conditions of using CHIIA-NLP program and by using CHIIA-NLP program you are agreeing to these terms and conditions.
###
Materials on this program is owned by, or licensed to, Dow Jones Factive. Material owned by Dow Jones Factive is subject to Copyright and our authorisation is required prior to use of the material. 
CHIIA-NLP's development and use is only for learning and research. No one should use these materials for commercial purposes.
