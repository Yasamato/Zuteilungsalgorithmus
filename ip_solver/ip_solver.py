import pandas as pd
import numpy as np
import mip
projects = pd.read_csv("projekte.csv", header=None)
students = pd.read_csv("schueler.csv", header=None)
num_students = len(students)
num_projects = len(projects)
get_student_name = lambda student_num: students.iloc[student_num].iloc[1]
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
    
punish_terms = [0, 1, 4, 9, 16, 1000]
ip.objective = mip.xsum(student_vars[student][j] * punish_terms[j] for student in range(len(students)) for j in range(6))

ip.verbose = 0
status = ip.optimize(max_seconds=10, )
if status == mip.OptimizationStatus.OPTIMAL:
    print("Optimization finished with a proven optimal solution")
elif status == mip.OptimizationStatus.FEASIBLE:
    print("Optimization finished with a solution that may be suboptimal.")
    print("Proceed with caution. Solution will be printed/outputted normally.")
    print("Please investigate the solution for any suspicions.")
    print("Feel free to contact me at fabianvonderwarth@gmail.com.")
else:
    print("Optimization terminated with an unrecoverable error.")
    print("Please contact me at fabianvonderwarth@gmail.com.")
    assert False

print(f"Objective value(Mean): {ip.objective_value}({ip.objective_value/num_students:.4f})")
assigned_project_num = len([x for x in range(num_projects) if project_vars[x].x == 1.])
print(f"Anzahl der Projekte die stattfinden: {assigned_project_num}/{num_projects}")

wishes_nums = [0 for _ in range(6)]
for j in range(6):
    for student in range(num_students):
        if student_vars[student][j].x == 1:
            wishes_nums[j] += 1
    if j < 5:
        print(f"Anzahl Schueler die die {j+1} te Wahl bekommen haben: {wishes_nums[j]}/{num_students}({wishes_nums[j]/num_students:.4f}%)")
    else:
        print(f"Anzahl Schueler ohne Projekt(%): {wishes_nums[j]}({wishes_nums[j]/num_students:.4f})")


# Prepare print for verteilungNachProjekten.csv
project_names, student_ids = [], []
for student in range(num_students):
    if student_vars[student][-1].x == 1.:
        project_names.append("None")
        student_ids.append(get_student_name(student))
for project in range(num_projects):
    project_name = projects.iloc[project].iloc[0]
    for var in relevant_student_vars[project]:
        _, student, wish_num = var.name.split("_")
        student, wish_num = int(student), int(wish_num)
        if var.x == 1. and students.iloc[student].iloc[2+wish_num] == project_name:
            project_names.append(project_name)
            student_ids.append(get_student_name(student))
np.savetxt("verteilungNachProjekten.csv", np.array([project_names, student_ids]).T, delimiter=",", header="Projekt ID, Schueler ID", fmt='%s', comments='')
# Prepare print for verteilungNachSchuelern.csv
project_names, wish_nums =[], []
student_ids = list(students[1])
classes = list(students[0])
for student in range(num_students):
    for j in range(6):
        if j == 5:
            project_names.append("None")
            wish_nums.append("None")
            continue
        if student_vars[student][j].x == 1.:
            project_names.append(students.iloc[student].iloc[3+j])
            wish_nums.append(str(j+1))
            break

np.savetxt("verteilungNachSchuelern.csv", np.array([classes, student_ids, project_names, wish_nums]).T, delimiter=",", header="Klasse, Schueler ID, Projekt ID, Wunsch Nr.", fmt='%s', comments='')
