import pandas as pd
import mip
projects = pd.read_csv("projekte.csv", header=None)
students = pd.read_csv("schueler.csv", header=None)
num_students = len(students)
num_projects = len(projects)
ip = mip.Model(solver_name=mip.CBC)
student_vars = []
for student in range(num_students):
    student_vars.append([ip.add_var(var_type=mip.BINARY, name=f"x_{student}_{j+1}") for j in range(6)])
    ip += mip.xsum(student_vars[-1][j] for j in range(6)) == 1

project_vars = []
relevant_student_vars = []
for project in range(num_projects):
    project_vars.append(ip.add_var(var_type=mip.BINARY, name=f"p_{project}"))
    relevant_student_vars.append([
        student_vars[student][var_num] for student in range(num_students) for var_num in range(5) if
        students.iloc[student].iloc[3+var_num] == projects.iloc[project].iloc[0]
        ])
    ip += mip.xsum(relevant_student_vars[-1]) <= project_vars[project] * projects.iloc[project].iloc[2], f"project_{project}_ub"
    ip += mip.xsum(relevant_student_vars[-1]) >= project_vars[project] * projects.iloc[project].iloc[1], f"project_{project}_lb"
    
punish_terms = [0, 1, 2, 4, 9, 16, 1000]
ip.objective = mip.xsum(student_vars[student][j] * punish_terms[j] for student in range(len(students)) for j in range(6))


status = ip.optimize(max_seconds=10)
for v in ip.vars:
    print(f"{v.name}:{v.x}")