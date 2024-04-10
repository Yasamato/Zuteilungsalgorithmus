import pandas as pd
import numpy as np
import mip
import time

projects = np.array(pd.read_csv("projekte.csv", header=None))
students = np.array(pd.read_csv("schueler.csv", header=None))
num_students = len(students)
num_projects = len(projects)
print(f"Succesfully loaded {num_students} students and {num_projects} projects!")
get_student_name = lambda student_num: students[student_num, 1]
print("Setting up the IP Model...")
time_start = time.time()
ip = mip.Model(solver_name=mip.CBC)
student_vars = []
for student in range(num_students):
    student_vars.append([ip.add_var(var_type=mip.BINARY, name=f"x_{student}_{j+1}") for j in range(6)])
    ip += mip.xsum(student_vars[-1][j] for j in range(6)) == 1

project_vars = []
relevant_student_vars = []
for project in range(num_projects):
    project_vars.append(ip.add_var(var_type=mip.BINARY, name=f"p_{project}"))
    project_name = projects[project, 0]
    relevant_student_vars.append([
        student_vars[student][var_num] for student in range(num_students) for var_num in range(5) if
        students[student, 3+var_num] == project_name
        ])
    ip += mip.xsum(relevant_student_vars[-1]) <= project_vars[project] * projects[project, 2], f"project_{project}_ub"
    ip += mip.xsum(relevant_student_vars[-1]) >= project_vars[project] * projects[project, 1], f"project_{project}_lb"
    
punish_terms = [0, 1, 4, 9, 16, 1000]
ip.objective = mip.xsum(student_vars[student][j] * punish_terms[j] for student in range(len(students)) for j in range(6))
print(f"Finished setting up model in {time.time()-time_start:.2f}s!. Starting solving procedure...")
time_start = time.time()
ip.verbose = 0
ip.start = [(student_vars[student][-1], 1.) for student in range(num_students)]
status = ip.optimize(max_seconds=10, )
print(f"Solving procedure terminated in {time.time()-time_start:.2f}s!")
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
orig_objective_value = ip.objective_value
print(f"Objective value(Mean): {orig_objective_value}({orig_objective_value/num_students:.4f})")
assigned_project_num = len([x for x in range(num_projects) if project_vars[x].x == 1.])
print(f"Amount of projects with sufficent participants: {assigned_project_num}/{num_projects}")

wishes_nums = [0 for _ in range(6)]
for j in range(6):
    for student in range(num_students):
        if student_vars[student][j].x == 1:
            wishes_nums[j] += 1
    if j < 5:
        print(f"Amount of students assigned their {j+1} th wish: {wishes_nums[j]}/{num_students}({wishes_nums[j]/num_students:.4f}%)")
    else:
        print(f"Amount of students not assigned to any project(%): {wishes_nums[j]}({wishes_nums[j]/num_students:.4f})")

print("Saving resulting distribution to verteilungNachProjekten.csv and verteilungNachSchuelern.csv")
# Prepare print for verteilungNachProjekten.csv
project_names, student_ids = [], []
for student in range(num_students):
    if student_vars[student][-1].x == 1.:
        project_names.append("None")
        student_ids.append(get_student_name(student))
for project in range(num_projects):
    project_name = projects[project, 0]
    for var in relevant_student_vars[project]:
        _, student, wish_num = var.name.split("_")
        student, wish_num = int(student), int(wish_num)
        if var.x == 1. and students[student, 2+wish_num] == project_name:
            project_names.append(project_name)
            student_ids.append(get_student_name(student))
np.savetxt("verteilungNachProjekten.csv", np.array([project_names, student_ids]).T, delimiter=",", header="Projekt ID, Schueler ID", fmt='%s', comments='')
# Prepare print for verteilungNachSchuelern.csv
project_names, wish_nums =[], []
student_ids = list(students[:, 1])
classes = list(students[:, 0])
for student in range(num_students):
    for j in range(6):
        if j == 5:
            project_names.append("None")
            wish_nums.append("None")
            continue
        if student_vars[student][j].x == 1.:
            project_names.append(students[student, 3+j])
            wish_nums.append(str(j+1))
            break

np.savetxt("verteilungNachSchuelern.csv", np.array([classes, student_ids, project_names, wish_nums]).T, delimiter=",", header="Klasse, Schueler ID, Projekt ID, Wunsch Nr.", fmt='%s', comments='')

# Try computing suggestions
print("Computing suggestions, this may take a while...")
time_start = time.time()
MAX_UB_INCREASE = 3
MIN_LB_PERCENTAGE=0.5
PRINT_TOP_N_UB_SUGGESTIONS=5
project_vars_original = [var.x for var in project_vars]
ub_suggestions = []
for project in range(num_projects):
    if project_vars_original[project] == 0.:
        project_lb = projects[project, 1]
        new_lb = project_lb -1
        while new_lb >= MIN_LB_PERCENTAGE * project_lb:
            ip.remove(ip.constr_by_name(f"project_{project}_lb"))
            ip += mip.xsum(relevant_student_vars[project]) >= project_vars[project] * new_lb, f"project_{project}_lb"
            ip.optimize(max_seconds=10)
            if ip.objective_value < orig_objective_value:
                assert project_vars[project].x == 1.
                print(f"-Lowering the lower bound of attendants of project {projects[project, 0]} from {project_lb} to {new_lb} would produce a distribution with objective value {ip.objective_value} in which the project is also used.")
                break
            else:
                new_lb -= 1
        ip.remove(ip.constr_by_name(f"project_{project}_lb"))
        ip += mip.xsum(relevant_student_vars[project]) >= project_vars[project] * project_lb, f"project_{project}_lb"
        
    else:
        project_ub = projects[project, 2]
        for i in range(MAX_UB_INCREASE):
            new_ub = project_ub + 1 + i
            ip.remove(ip.constr_by_name(f"project_{project}_ub"))
            ip += mip.xsum(relevant_student_vars[project]) <= project_vars[project] * new_ub, f"project_{project}_ub"
            ip.optimize(max_seconds=10)
            if ip.objective_value < orig_objective_value:
                ub_suggestions.append((ip.objective_value, f"-Increasing the upper bound of attendants of project {projects[project, 0]} from {project_ub} to {new_ub} would produce a distribution with objective value {ip.objective_value}, an improvement of {orig_objective_value- ip.objective_value}."))
        ip.remove(ip.constr_by_name(f"project_{project}_ub"))
        ip += mip.xsum(relevant_student_vars[project]) <= project_vars[project] * project_ub, f"project_{project}_ub"
ub_suggestions = sorted(ub_suggestions, key=lambda x: x[0])
for i in range(PRINT_TOP_N_UB_SUGGESTIONS):
    print(ub_suggestions[i][1])
   

print("Notes:\n-Applying more than one suggestion can produce non-additive interference affects to the resulting distributions objective value.\nApply the suggestions to the data and rerun the script to see the exact effects suggestions have to the objective value and the resulting distribution.")
print("-Make sure to backup the data before you apply any suggestions.")
print("-To see more or all suggestions, increase parameter PRINT_TOP_N_UB_SUGGESTIONS or investigate the variable ub_suggestions.")
print(f"Suggestions subroutine succesfully terminated in {time.time()-time_start:.2f}s! Exiting.")