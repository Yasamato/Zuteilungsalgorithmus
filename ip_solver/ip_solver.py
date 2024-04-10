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
#####
# The IP is a Integer Program, consisting of two sets of variables
# For each student i, we have a variable x_i_j where j goes from 1 to the amount of wishes 
# the students had to make plus one. It is added as a binary variable, i.e. x_i_j \in {0,1}
# The semantic is as follows: x_i_j = 1 <=> "Student i is assigned to their j-th wish" (where if j is higher than the amount of wishes the student made, it means that the student is not assigned to any project)
# For each project i, we have a variable p_i, which is added as a binary variable.
# The semantic is as follows: p_i = 1 <=> "The amount of students assigned to this project must be between the lower and upper bound given in the data",
# and p_i = 0 <=> The amount of students assigned to this project must be zero. 
ip = mip.Model(solver_name=mip.CBC)
student_vars = []
for student in range(num_students):
    student_vars.append([ip.add_var(var_type=mip.BINARY, name=f"x_{student}_{j+1}") for j in range(6)])
    ip += mip.xsum(student_vars[-1][j] for j in range(6)) == 1
    # This constraint sums up the x_i_j over j for each student i, ensuring that each
    # student is assigned to one of their wishes (or None project if largest j is chosen)

project_vars = []
relevant_student_vars = []
for project in range(num_projects):
    project_vars.append(ip.add_var(var_type=mip.BINARY, name=f"p_{project}"))
    project_name = projects[project, 0]
    relevant_student_vars.append([
        student_vars[student][var_num] for student in range(num_students) for var_num in range(5) if
        students[student, 3+var_num] == project_name
        ])
    #These two constraints ensure that if p_i =0 => No student assigned to the project
    #and p_i = 1 => Amount of students assigned to project is in the corresponding range
    
    ip += mip.xsum(relevant_student_vars[-1]) <= project_vars[project] * projects[project, 2], f"project_{project}_ub"
    ip += mip.xsum(relevant_student_vars[-1]) >= project_vars[project] * projects[project, 1], f"project_{project}_lb"
    
punish_terms = [0, 1, 4, 9, 16, 1000]
#We optimize to minimize the sum of the penalty points. Penatly point are assigned for each
#assignment of a student to their j-th wish, with the amount of points given
#by the j-th entry in the list punish_terms.
ip.objective = mip.xsum(student_vars[student][j] * punish_terms[j] for student in range(len(students)) for j in range(6))
print(f"Finished setting up model in {time.time()-time_start:.2f}s!. Starting solving procedure...")
time_start = time.time()
ip.verbose = 0
#A feasible solution is given by assigning every student to No project, i.e. x_i_j = 1 for the largest j.
#Giving the solver a feasible solution helps speed up solving time.
ip.start = [(student_vars[student][-1], 1.) for student in range(num_students)]
status = ip.optimize(max_seconds=10, )
print(f"Solving procedure terminated in {time.time()-time_start:.2f}s!")

# Print some info about optimization status and the distribution that was calculated
# Exact distribution is outputted below in a csv file.
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
        print(f"Amount of students assigned their wish number {j+1}: {wishes_nums[j]}/{num_students}({wishes_nums[j]/num_students:.4f}%)")
    else:
        print(f"Amount of students not assigned to any project(%): {wishes_nums[j]}({wishes_nums[j]/num_students:.4f})")

print("Saving resulting distribution to verteilungNachProjekten.csv and verteilungNachSchuelern.csv")

# Prepare print to csv files
lines = []
for student in range(num_students):
    for j in range(6):
        if j == 5:
            lines.append((students[student, 0], students[student, 1], "None", "None"))
            continue
        if student_vars[student][j].x == 1.:
            lines.append((students[student, 0], students[student, 1], students[student, 3+j], str(j+1)))
            break
lines = sorted(lines, key=lambda x: x[0])
np.savetxt("verteilungNachSchuelern.csv", np.array(lines), delimiter=",", header="Klasse, Schueler ID, Projekt ID, Wunsch Nr.", fmt='%s', comments='')
lines = sorted(lines, key=lambda x: x[2])
np.savetxt("verteilungNachProjekten.csv", np.array(lines)[:, [2, 1, 3]], delimiter=",", header="Projekt ID, Schueler ID, Wunsch Nr.", fmt='%s', comments='')


# Compute suggestions
print("Computing suggestions, this may take a while...")
time_start = time.time()
MAX_UB_INCREASE = 3
MIN_LB_PERCENTAGE=0.5
PRINT_TOP_N_UB_SUGGESTIONS=5
project_vars_original = [var.x for var in project_vars]
ub_suggestions = []
for project in range(num_projects):
    #Project that dont have a sufficent amount of participants have their variable set to 0
    if project_vars_original[project] == 0.:
        project_lb = projects[project, 1]
        new_lb = project_lb -1
        #Try lowering the lower bound, reoptimizing. Check if optimization value
        #decreases, in which case the project is now used in the new distribution.
        while new_lb >= MIN_LB_PERCENTAGE * project_lb:
            ip.remove(ip.constr_by_name(f"project_{project}_lb"))
            ip += mip.xsum(relevant_student_vars[project]) >= project_vars[project] * new_lb, f"project_{project}_lb"
            ip.optimize(max_seconds=10)
            if ip.objective_value < orig_objective_value:
                assert project_vars[project].x == 1.
                print(f"-Lowering the lower bound of attendants of project {projects[project, 0]} from {project_lb} to {new_lb} would produce a distribution with objective value {ip.objective_value} in which the project is now also used.")
                break
            else:
                new_lb -= 1
        #Revert to the original model.
        ip.remove(ip.constr_by_name(f"project_{project}_lb"))
        ip += mip.xsum(relevant_student_vars[project]) >= project_vars[project] * project_lb, f"project_{project}_lb"
        
    else:
        #This is a project with a sufficient amount of participants, i.e. the variable is set to 1
        #Try upping the upper bound, reoptimizing. Check if optimizatin value decreases.
        #Save improved values, from which the best will be printed later.
        project_ub = projects[project, 2]
        for i in range(MAX_UB_INCREASE):
            new_ub = project_ub + 1 + i
            ip.remove(ip.constr_by_name(f"project_{project}_ub"))
            ip += mip.xsum(relevant_student_vars[project]) <= project_vars[project] * new_ub, f"project_{project}_ub"
            ip.optimize(max_seconds=10)
            if ip.objective_value < orig_objective_value:
                ub_suggestions.append((ip.objective_value, f"-Increasing the upper bound of attendants of project {projects[project, 0]} from {project_ub} to {new_ub} would produce a distribution with objective value {ip.objective_value}, an improvement of {orig_objective_value- ip.objective_value}."))
        #Revert to the original model.
        ip.remove(ip.constr_by_name(f"project_{project}_ub"))
        ip += mip.xsum(relevant_student_vars[project]) <= project_vars[project] * project_ub, f"project_{project}_ub"
# Print the best suggestions for relaxing upper bounds of the model.
ub_suggestions = sorted(ub_suggestions, key=lambda x: x[0])
for i in range(min(PRINT_TOP_N_UB_SUGGESTIONS, len(ub_suggestions))):
    print(ub_suggestions[i][1])
   

print("Notes:\n-Applying more than one suggestion can produce non-additive interference affects to the resulting distributions objective value.\nApply the suggestions to the data and rerun the script to see the exact effects suggestions have to the objective value and the resulting distribution.")
print("-Make sure to backup the data before you apply any suggestions.")
print("-To see more or all suggestions, increase parameter PRINT_TOP_N_UB_SUGGESTIONS or investigate the variable ub_suggestions.")
print(f"Suggestions subroutine succesfully terminated in {time.time()-time_start:.2f}s! Exiting.")